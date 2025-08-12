<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\Smm\JapClient;

class SmmPollOrders extends Command
{
    protected $signature = 'smm:poll-orders 
                            {--limit=100 : Batas order per eksekusi} 
                            {--provider= : Filter nama provider (contoh: JAP)} 
                            {--dry-run : Simulasi saja, tanpa update DB}';

    protected $description = 'Polling status order dari provider (JAP) dan update status lokal';

    public function handle(): int
    {
        $limit    = (int) $this->option('limit');
        $provName = $this->option('provider');
        $dryRun   = (bool) $this->option('dry-run');

        $q = Order::query()
            ->with(['service.provider'])
            ->whereNotNull('provider_order_id')
            ->whereIn('status', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
            ])
            ->orderByDesc('id');

        if ($provName) {
            $q->whereHas('service.provider', fn($qq) => $qq->where('name', $provName));
        }

        $orders = $q->limit($limit)->get();
        if ($orders->isEmpty()) {
            $this->info('Tidak ada order pending/processing untuk dipoll.');
            return self::SUCCESS;
        }

        $updated = 0;
        $errors = 0;

        foreach ($orders as $o) {
            $provider = $o->service->provider;
            $client = new JapClient(
                baseUrl: env('JAP_BASE_URL'),
                apiKey: env('JAP_API_KEY'),
                providerId: $provider->id
            );

            try {
                $resp = $client->orderStatus(['order' => (string)$o->provider_order_id]);

                $provStatus = strtolower((string)($resp['status'] ?? ''));
                $mapped = $this->mapStatus($provStatus, $resp);

                $meta = $o->meta ?? [];
                $meta['last_status_response'] = $resp;
                $meta['status_history'][] = [
                    'at'     => now()->toDateTimeString(),
                    'status' => $provStatus,
                    'remains' => $resp['remains'] ?? null,
                ];

                if (!$dryRun) {
                    $o->update([
                        'status' => $mapped,
                        'meta'   => $meta,
                    ]);
                }

                $this->line("Order #{$o->id} ({$o->provider_order_id}) : {$provStatus} => {$mapped}");
                $updated++;
            } catch (\Throwable $e) {
                $errors++;
                $this->error("Order #{$o->id} error: " . $e->getMessage());
                if (!$dryRun) {
                    $meta = $o->meta ?? [];
                    $meta['status_error'][] = ['at' => now()->toDateTimeString(), 'msg' => $e->getMessage()];
                    $o->update(['meta' => $meta]);
                }
            }
        }

        $this->info("Selesai. Updated: {$updated}, Errors: {$errors}" . ($dryRun ? ' [DRY RUN]' : ''));
        return self::SUCCESS;
    }

    /**
     * Map status provider ke status lokal.
     */
    protected function mapStatus(string $provStatus, array $resp): string
    {
        $provStatus = trim($provStatus);

        // Normalisasi umum dari panel SMM:
        if ($provStatus === 'completed' || $provStatus === 'success' || ($resp['remains'] ?? null) === 0) {
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

        // Default: treat as processing jika tak dikenal
        return Order::STATUS_PROCESSING;
    }
}
