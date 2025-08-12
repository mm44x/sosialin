<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Smm\JapClient;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class OrderSubmitJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;                 // max retry
    public array $backoff = [5, 15, 30];   // detik

    public function uniqueId(): string
    {
        // Pastikan satu order tidak diproses dobel
        return 'order-submit-' . $this->orderId;
    }

    public function __construct(
        public int $orderId,
        public string $idempotencyKey
    ) {}

    public function handle(WalletService $wallet): void
    {
        $order = Order::with(['service.provider', 'user'])->findOrFail($this->orderId);

        // Jika sudah punya provider_order_id, abaikan (idempoten)
        if ($order->provider_order_id) {
            return;
        }

        $service  = $order->service;
        $provider = $service->provider;

        $client = new JapClient(
            baseUrl: env('JAP_BASE_URL'),
            apiKey: env('JAP_API_KEY'),
            providerId: $provider->id
        );

        // Tandai sedang dikirim + simpan idempotency key
        $meta = $order->meta ?? [];
        $meta['idempotency_key'] = $this->idempotencyKey;
        $order->update([
            'status' => Order::STATUS_PROCESSING,
            'meta'   => $meta,
        ]);

        // Panggil provider
        $resp = $client->addOrder([
            'service'  => (string) $service->external_service_id,
            'link'     => (string) $order->link,
            'quantity' => (int) $order->quantity,
        ]);

        $provId = $resp['order'] ?? $resp['order_id'] ?? $resp['id'] ?? null;
        if (!$provId) {
            // Trigger retry dengan melempar exception
            throw new \RuntimeException('No provider order id in response');
        }

        // Sukses
        $order->update([
            'provider_order_id' => (string) $provId,
            'status'            => Order::STATUS_PROCESSING,
            'meta'              => array_merge($order->meta ?? [], ['provider_response' => $resp]),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        // Semua percobaan gagal → refund jika belum ada provider_order_id
        $order = Order::with(['service.provider', 'user'])->find($this->orderId);
        if (!$order || $order->provider_order_id) return;

        $wallet = app(WalletService::class);

        // Hindari refund ganda: cek flag
        $meta = $order->meta ?? [];
        if (Arr::get($meta, 'refund_done')) return;

        $wallet->credit($order->user_id, (float)$order->cost, 'refund', [
            'reason'   => 'order_submit_failed',
            'order_id' => $order->id,
            'error'    => $e->getMessage(),
        ]);

        $meta['refund_done'] = true;
        $meta['submit_failed_msg'] = $e->getMessage();

        $order->update([
            'status' => Order::STATUS_ERROR,
            'meta'   => $meta,
        ]);
    }
}
