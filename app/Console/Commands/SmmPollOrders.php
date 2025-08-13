<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class SmmPollOrders extends Command
{
    protected $signature = 'smm:poll-orders
        {--limit=100 : Maksimum order yang diproses sekali jalan}
        {--only= : Filter status awal: pending|processing}
        {--order-id= : Poll satu order tertentu}
        {--provider= : Filter provider (ID atau nama persis)}';

    protected $description = 'Polling status order dari provider (JAP) dan update status lokal + refund idempoten';

    public function handle(): int
    {
        $limit    = (int) $this->option('limit');
        $only     = $this->option('only');
        $orderId  = $this->option('order-id');
        $provOpt  = $this->option('provider');

        $statuses = ['pending', 'processing'];
        if ($only && in_array(strtolower($only), ['pending', 'processing'], true)) {
            $statuses = [strtolower($only)];
        }

        $q = Order::with(['service.provider'])
            ->when($orderId, fn($qq) => $qq->where('id', (int) $orderId))
            ->whereIn('status', $statuses)
            ->orderBy('id');

        // Filter provider
        if ($provOpt) {
            $q->whereHas('service.provider', function ($qq) use ($provOpt) {
                if (is_numeric($provOpt)) {
                    $qq->where('id', (int) $provOpt);
                } else {
                    $qq->whereRaw('LOWER(name) = ?', [mb_strtolower($provOpt)]);
                }
            });
        }

        $orders = $q->limit(max(1, min($limit, 500)))->get();

        if ($orders->isEmpty()) {
            $this->info('Tidak ada order untuk dipoll.');
            return self::SUCCESS;
        }

        $this->info("Memproses {$orders->count()} order ...");

        $ok = 0;
        $err = 0;
        $completed = 0;
        $canceled = 0;
        $partial = 0;

        foreach ($orders as $order) {
            try {
                if (!$order->provider_order_id) {
                    $this->warn("Order #{$order->id} belum punya provider_order_id — lewati.");
                    $err++;
                    continue;
                }

                $service  = $order->service;
                $provider = $service?->provider;

                $baseUrl = $provider?->base_url ?: env('JAP_BASE_URL');
                $apiKey  = $provider?->api_key  ?: env('JAP_API_KEY');

                if (empty($baseUrl) || empty($apiKey)) {
                    $this->warn("Provider kosong kredensial untuk order #{$order->id} — lewati.");
                    $err++;
                    continue;
                }

                $client = new \App\Services\Smm\JapClient(
                    baseUrl: $baseUrl,
                    apiKey: $apiKey,
                    providerId: $provider?->id
                );

                $resp = $client->orderStatus(['order' => (string)$order->provider_order_id]);
                $provStatus = strtolower((string)($resp['status'] ?? ''));
                $mapped     = $this->mapProviderStatus($provStatus, $resp);
                $remains    = isset($resp['remains']) ? (float)$resp['remains'] : null;

                DB::transaction(function () use ($order, $mapped, $resp, $remains, &$completed, &$canceled, &$partial) {
                    /** @var \App\Models\Order $locked */
                    $locked = Order::where('id', $order->id)->lockForUpdate()->first();

                    $meta = $locked->meta ?? [];
                    $meta['last_status_response'] = $resp;
                    $meta['status_history'][] = [
                        'at'      => now()->toDateTimeString(),
                        'status'  => strtolower((string)($resp['status'] ?? '')),
                        'mapped'  => $mapped,
                        'remains' => $remains,
                        'source'  => 'poll',
                    ];

                    // Update status jika berubah
                    if ($locked->status !== $mapped) {
                        $locked->status = $mapped;
                    }

                    // Refund rules — gunakan flag yang sama dengan manual check
                    if ($mapped === \App\Models\Order::STATUS_CANCELED) {
                        if (!Arr::get($meta, 'refund_done')) {
                            app(\App\Services\WalletService::class)->credit($locked->user_id, (float)$locked->cost, 'refund', [
                                'reason'   => 'canceled_by_provider_poll',
                                'order_id' => $locked->id,
                            ]);
                            $meta['refund_done'] = true;
                            $canceled++;
                        }
                    } elseif ($mapped === \App\Models\Order::STATUS_PARTIAL && $remains !== null) {
                        if (!Arr::get($meta, 'partial_refund_done')) {
                            $qty    = max(1, (float)$locked->quantity);
                            $ratio  = max(0, min(1, $remains / $qty)); // 0..1
                            $refund = round(((float)$locked->cost) * $ratio, 2);
                            if ($refund > 0) {
                                app(\App\Services\WalletService::class)->credit($locked->user_id, $refund, 'refund', [
                                    'reason'   => 'partial_refund_poll',
                                    'order_id' => $locked->id,
                                    'remains'  => $remains,
                                    'ratio'    => $ratio,
                                ]);
                                $meta['partial_refund_done']   = true;
                                $meta['partial_refund_amount'] = $refund;
                                $partial++;
                            }
                        }
                    } elseif ($mapped === \App\Models\Order::STATUS_COMPLETED) {
                        $completed++;
                    }

                    $locked->meta = $meta;
                    $locked->save();
                });

                $ok++;
            } catch (\Throwable $e) {
                $this->error("Order #{$order->id} gagal dipoll: " . $e->getMessage());
                $err++;
                // lanjut ke order berikutnya
            }
        }

        $this->line("OK: {$ok}, Error: {$err}, Completed+: {$completed}, Partial refund+: {$partial}, Canceled refund+: {$canceled}");
        return self::SUCCESS;
    }

    /** Pemetaan status provider → status internal. */
    private function mapProviderStatus(string $provStatus, array $resp): string
    {
        $provStatus = trim(strtolower($provStatus));
        if (
            $provStatus === 'completed' || $provStatus === 'success' ||
            (($resp['remains'] ?? null) === 0 || ($resp['remains'] ?? null) === '0')
        ) {
            return \App\Models\Order::STATUS_COMPLETED;
        }
        if ($provStatus === 'partial') {
            return \App\Models\Order::STATUS_PARTIAL;
        }
        if ($provStatus === 'canceled' || $provStatus === 'cancelled') {
            return \App\Models\Order::STATUS_CANCELED;
        }
        if ($provStatus === 'processing' || $provStatus === 'in progress' || $provStatus === 'inprogress') {
            return \App\Models\Order::STATUS_PROCESSING;
        }
        if ($provStatus === 'pending' || $provStatus === '') {
            return \App\Models\Order::STATUS_PENDING;
        }
        return \App\Models\Order::STATUS_PROCESSING;
    }
}
