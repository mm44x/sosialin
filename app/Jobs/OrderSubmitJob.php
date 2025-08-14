<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Smm\JapClient;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderSubmitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maks percobaan */
    public int $tries = 3;

    /** Jeda antar percobaan (detik) */
    public $backoff = [10, 30, 90];

    /** Timeout job (detik) */
    public int $timeout = 30;

    public function __construct(
        public int $orderId,
        public string $idempotencyKey
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        /** @var Order $order */
        $order = Order::with(['service.provider'])->findOrFail($this->orderId);

        // 1) Idempoten: jika sudah punya provider_order_id, anggap selesai
        if ($order->provider_order_id) {
            return;
        }

        // 2) Safety: hanya proses jika status masih pending/processing
        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PROCESSING], true)) {
            return;
        }

        // 3) Siapkan client untuk provider terkait service
        $service  = $order->service;
        $provider = $service->provider;

        $baseUrl = $provider->base_url ?: env('JAP_BASE_URL');
        $apiKey  = $provider->api_key  ?: env('JAP_API_KEY');

        if (!$baseUrl || !$apiKey) {
            throw new \RuntimeException('Provider base_url/api_key kosong.');
        }

        // 4) Payload ke provider
        $payload = [
            'service'  => (string) $service->external_service_id,
            'link'     => (string) $order->link,
            'quantity' => (int) $order->quantity,
        ];

        $client = new JapClient(
            baseUrl: $baseUrl,
            apiKey: $apiKey,
            providerId: $provider->id
        );

        // 5) Panggil provider
        $resp = $client->addOrder($payload);

        // Ambil berbagai kemungkinan key ID dari respon
        $provId = $resp['order'] ?? $resp['order_id'] ?? $resp['id'] ?? null;

        // 6) Simpan hasil secara atomik
        DB::transaction(function () use ($order, $resp, $provId) {
            /** @var Order $locked */
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            // Tambah counter attempts & simpan respon terakhir
            $meta = $locked->meta ?? [];
            $meta['attempts'] = (int) (($meta['attempts'] ?? 0) + 1);
            $meta['provider_response_submit'] = $resp;

            // Jika pada saat bersamaan sudah terisi (race), jangan menimpa
            if ($locked->provider_order_id) {
                $locked->meta = $meta;
                $locked->save();
                return;
            }

            if ($provId) {
                // Sukses
                $locked->update([
                    'provider_order_id' => (string) $provId,
                    'status'            => Order::STATUS_PROCESSING,
                    'meta'              => $meta,
                ]);
            } else {
                // Tidak ada ID -> lempar agar retry
                $locked->meta = $meta;
                $locked->save();
                throw new \RuntimeException('Provider tidak mengembalikan order id.');
            }
        });
    }

    /**
     * Dipanggil otomatis setelah semua percobaan gagal.
     * Lakukan refund SEKALI saja secara aman.
     */
    public function failed(\Throwable $e): void
    {
        try {
            /** @var Order|null $order */
            $order = Order::find($this->orderId);
            if (!$order) return;

            DB::transaction(function () use ($order, $e) {
                /** @var Order $locked */
                $locked = Order::where('id', $order->id)->lockForUpdate()->first();

                // Jika pada akhirnya sempat berhasil, jangan refund
                if ($locked->provider_order_id) {
                    return;
                }

                $meta = $locked->meta ?? [];

                // Refund cuma sekali
                if (!Arr::get($meta, 'submit_refund_done')) {
                    app(WalletService::class)->credit($locked->user_id, (float) $locked->cost, 'refund', [
                        'reason'   => 'provider_submit_failed_after_retries',
                        'order_id' => $locked->id,
                        'error'    => $e->getMessage(),
                        'attempts' => $meta['attempts'] ?? null,
                    ]);
                    $meta['submit_refund_done'] = true;
                }

                // Tandai order error + catat error terakhir
                $meta['submit_last_error'] = $e->getMessage();
                $locked->update([
                    'status' => Order::STATUS_ERROR,
                    'meta'   => $meta,
                ]);
            });
        } catch (\Throwable $ex) {
            Log::error('ORDER_SUBMIT_JOB_FAILED_HANDLER_ERROR', [
                'order_id' => $this->orderId,
                'error'    => $ex->getMessage(),
            ]);
        }
    }
}
