<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\Smm\JapClient;
use App\Services\WalletService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PollOrdersCommand extends Command
{
    protected $signature = 'smm:poll-orders 
        {--limit=100 : Batas maksimum order yang diproses per eksekusi} 
        {--dry-run : Simulasi tanpa menulis ke DB / tanpa refund}';

    protected $description = 'Polling status order dari provider untuk order pending/processing; update status + refund (partial/canceled).';

    public function handle(): int
    {
        $limit   = (int) $this->option('limit');
        $dryRun  = (bool) $this->option('dry-run');

        $orders = Order::with(['service.provider', 'user'])
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
            ->whereNotNull('provider_order_id')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Tidak ada order untuk dipoll.');
            return self::SUCCESS;
        }

        $this->info("Memproses {$orders->count()} order... (dry-run: " . ($dryRun ? 'YA' : 'TIDAK') . ")");

        $updated = 0;
        $completed = 0;
        $partial = 0;
        $canceled = 0;
        $refunded = 0;
        $errors = 0;

        foreach ($orders as $order) {
            try {
                $service  = $order->service;
                $provider = $service->provider;

                $client = new JapClient(
                    baseUrl: env('JAP_BASE_URL'),
                    apiKey: env('JAP_API_KEY'),
                    providerId: $provider->id
                );

                $resp = $client->orderStatus(['order' => (string)$order->provider_order_id]);

                $provStatus = strtolower((string)($resp['status'] ?? ''));
                $mapped     = $this->mapStatus($provStatus, $resp);
                $remains    = isset($resp['remains']) ? (float)$resp['remains'] : null;

                // Simpan riwayat status + last response
                $meta = $order->meta ?? [];
                $meta['last_status_response'] = $resp;
                $meta['status_history'][] = [
                    'at'      => now()->toDateTimeString(),
                    'status'  => $provStatus,
                    'mapped'  => $mapped,
                    'remains' => $remains,
                ];

                if ($dryRun) {
                    $this->line("Order #{$order->id} DRY → provider='{$provStatus}', mapped='{$mapped}', remains=" . ($remains ?? 'null'));
                    $updated++;
                    continue;
                }

                // Kerjakan dalam transaksi + lock supaya tidak balapan
                DB::transaction(function () use ($order, $mapped, $remains, &$completed, &$partial, &$canceled, &$refunded, &$updated, $meta) {
                    // Re-load & lock row order
                    /** @var \App\Models\Order $locked */
                    $locked = Order::where('id', $order->id)->lockForUpdate()->first();

                    // Update status bila berubah
                    if ($locked->status !== $mapped) {
                        $locked->status = $mapped;
                    }

                    // Refund logic
                    if ($mapped === Order::STATUS_CANCELED) {
                        // Refund penuh sekali saja
                        $m = $locked->meta ?? [];
                        if (!Arr::get($m, 'refund_done')) {
                            app(WalletService::class)->credit($locked->user_id, (float)$locked->cost, 'refund', [
                                'reason'   => 'canceled_by_provider',
                                'order_id' => $locked->id,
                            ]);
                            $m['refund_done'] = true;
                            $locked->meta = array_merge($m, $meta);
                            $refunded++;
                        } else {
                            // tetap merge meta terbaru
                            $locked->meta = array_merge($m, $meta);
                        }
                        $canceled++;
                    } elseif ($mapped === Order::STATUS_PARTIAL && $remains !== null) {
                        // Refund proporsional sekali saja (berdasar 'remains')
                        $m = $locked->meta ?? [];
                        if (!Arr::get($m, 'partial_refund_done')) {
                            $qty = max(1, (float)$locked->quantity);
                            $ratio = max(0, min(1, $remains / $qty)); // 0..1
                            $refund = round(((float)$locked->cost) * $ratio, 2);

                            if ($refund > 0) {
                                app(WalletService::class)->credit($locked->user_id, $refund, 'refund', [
                                    'reason'   => 'partial_refund',
                                    'order_id' => $locked->id,
                                    'remains'  => $remains,
                                    'ratio'    => $ratio,
                                ]);
                                $m['partial_refund_done'] = true;
                                $m['partial_refund_amount'] = $refund;
                                $refunded++;
                            }
                            $locked->meta = array_merge($m, $meta);
                        } else {
                            $locked->meta = array_merge($m, $meta);
                        }
                        $partial++;
                    } elseif ($mapped === Order::STATUS_COMPLETED) {
                        $completed++;
                        // hanya update meta/history
                        $locked->meta = array_merge(($locked->meta ?? []), $meta);
                    } else {
                        // pending/processing → hanya update meta/history
                        $locked->meta = array_merge(($locked->meta ?? []), $meta);
                    }

                    $locked->save();
                    $updated++;
                });
            } catch (\Throwable $e) {
                $this->error("Order #{$order->id} error: " . $e->getMessage());
                Log::error('POLL_ORDERS_ERROR', ['order_id' => $order->id, 'e' => $e->getMessage()]);
                $errors++;
                continue;
            }
        }

        $this->info("Selesai. updated={$updated}, completed={$completed}, partial={$partial}, canceled={$canceled}, refunded={$refunded}, errors={$errors}");
        return self::SUCCESS;
    }

    protected function mapStatus(string $provStatus, array $resp): string
    {
        $provStatus = trim($provStatus);
        if ($provStatus === 'completed' || $provStatus === 'success' || (($resp['remains'] ?? null) === 0 || ($resp['remains'] ?? null) === '0')) {
            return Order::STATUS_COMPLETED;
        }
        if ($provStatus === 'partial') {
            return Order::STATUS_PARTIAL;
        }
        if ($provStatus === 'canceled' || $provStatus === 'cancelled') {
            return Order::STATUS_CANCELED;
        }
        if ($provStatus === 'processing' || $provStatus === 'in progress' || $provStatus === 'inprogress') {
            return Order::STATUS_PROCESSING;
        }
        if ($provStatus === 'pending' || $provStatus === '') {
            return Order::STATUS_PENDING;
        }
        return Order::STATUS_PROCESSING;
    }
}
