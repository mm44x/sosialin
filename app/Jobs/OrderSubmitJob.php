<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class OrderSubmitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    public $orderId;

    /** @var string */
    public $idempotencyKey;

    /** Jumlah maksimum percobaan (selain percobaan pertama) */
    public int $tries = 3;

    /** Jeda retry (detik) — eksponensial ringan */
    public function backoff(): array
    {
        return [10, 30, 90];
    }

    /**
     * @param  int    $orderId
     * @param  string $idempotencyKey  UUID yang sudah disimpan di order.meta['idempotency_key']
     */
    public function __construct(int $orderId, string $idempotencyKey)
    {
        $this->orderId = $orderId;
        $this->idempotencyKey = $idempotencyKey;
    }

    public function handle(): void
    {
        /** @var Order $order */
        $order = Order::with(['service.provider'])->findOrFail($this->orderId);

        // Idempoten lokal: jika sudah punya provider_order_id, anggap selesai
        if (!empty($order->provider_order_id)) {
            return;
        }

        // Ambil kredensial dari DB (fallback ke .env)
        $service  = $order->service;
        $provider = $service?->provider;

        $baseUrl = $provider?->base_url ?: env('JAP_BASE_URL');
        $apiKey  = $provider?->api_key  ?: env('JAP_API_KEY');

        if (empty($baseUrl) || empty($apiKey)) {
            throw new \RuntimeException('Provider base_url/api_key kosong.');
        }

        // Payload ke JAP
        $payload = [
            'service'  => (string) $service->external_service_id,
            'link'     => (string) $order->link,
            'quantity' => (int) $order->quantity,
            // Catatan: JAP v2 tidak mendukung idempotency header; kita simpan secara lokal di meta
        ];

        try {
            $client = new \App\Services\Smm\JapClient(
                baseUrl: $baseUrl,
                apiKey: $apiKey,
                providerId: $provider?->id
            );

            $resp = $client->addOrder($payload);

            // Ambil ID order dari berbagai kemungkinan field
            $provId = $resp['order'] ?? $resp['order_id'] ?? $resp['id'] ?? null;
            if (!$provId) {
                // Tidak ada ID — biar dipicu retry
                throw new \RuntimeException('Provider tidak mengembalikan order id.');
            }

            // Update sukses (PROCESSING) + simpan meta
            DB::transaction(function () use ($order, $resp, $provId) {
                /** @var Order $locked */
                $locked = Order::where('id', $order->id)->lockForUpdate()->first();

                // Jika pada saat bersamaan sudah terisi (race), hormati yang ada
                if (!empty($locked->provider_order_id)) {
                    return;
                }

                $meta = $locked->meta ?? [];
                $meta['last_submit_response'] = $resp;
                $meta['attempts'] = (int) (($meta['attempts'] ?? 0) + 1);

                $locked->update([
                    'provider_order_id' => (string) $provId,
                    'status'            => Order::STATUS_PROCESSING,
                    'meta'              => $meta,
                ]);
            });
        } catch (\Throwable $e) {
            // Simpan error terakhir untuk observabilitas
            try {
                DB::transaction(function () use ($order, $e) {
                    /** @var Order $locked */
                    $locked = Order::where('id', $order->id)->lockForUpdate()->first();
                    $meta = $locked->meta ?? [];
                    $meta['last_submit_error'] = $e->getMessage();
                    $meta['attempts'] = (int) (($meta['attempts'] ?? 0) + 1);
                    $locked->meta = $meta;
                    $locked->save();
                });
            } catch (\Throwable) {
                // abaikan error penyimpanan meta
            }

            // Lempar lagi supaya queue worker melakukan retry sesuai $tries/$backoff
            throw $e;
        }
    }

    /**
     * Dipanggil Laravel saat job melebihi jumlah percobaan.
     * Di sini kita lakukan REFUND sekali saja + tandai order ERROR.
     */
    public function failed(\Throwable $exception): void
    {
        try {
            DB::transaction(function () use ($exception) {
                /** @var Order $order */
                $order = Order::where('id', $this->orderId)->lockForUpdate()->first();
                if (!$order) return;

                $meta = $order->meta ?? [];

                // Refund sekali saja
                if (empty($meta['submit_refund_done'])) {
                    app(WalletService::class)->credit($order->user_id, (float)$order->cost, 'refund', [
                        'reason'   => 'submit_failed_after_retries',
                        'order_id' => $order->id,
                        'error'    => $exception->getMessage(),
                        'attempts' => (int)($meta['attempts'] ?? 0),
                    ]);
                    $meta['submit_refund_done'] = true;
                }

                $meta['submit_failed_at'] = now()->toDateTimeString();
                $meta['submit_failed_error'] = $exception->getMessage();

                // Tandai status ERROR jika belum selesai
                if (empty($order->provider_order_id)) {
                    $order->status = Order::STATUS_ERROR;
                }

                $order->meta = $meta;
                $order->save();
            });
        } catch (\Throwable) {
            // Jangan biarkan failed() melempar error baru
        }
    }
}
