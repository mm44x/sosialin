<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Provider;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SmmSyncServices extends Command
{
    protected $signature = 'smm:sync-services {--provider=} {--deactivate-missing}';
    protected $description = 'Sync daftar layanan dari JAP untuk provider tertentu atau semua provider aktif';

    public function handle(): int
    {
        $opt = $this->option('provider');

        // Tentukan target providers
        $providers = collect();
        if ($opt) {
            $providers = Provider::query()
                ->when(
                    is_numeric($opt),
                    fn($q) => $q->where('id', (int)$opt),
                    fn($q) => $q->whereRaw('LOWER(name) = ?', [mb_strtolower($opt)])
                )->get();

            if ($providers->isEmpty()) {
                $this->warn("Provider '{$opt}' tidak ditemukan. Pakai fallback .env.");
            }
        }

        if ($providers->isEmpty()) {
            // Jika tidak ada di DB, jalan dengan .env sebagai satu "provider virtual"
            $providers = collect([(object) [
                'id'        => 0,
                'name'      => 'ENV',
                'base_url'  => env('JAP_BASE_URL'),
                'api_key'   => env('JAP_API_KEY'),
                'active'    => true,
            ]]);
        }

        foreach ($providers as $p) {
            if (empty($p->base_url) || empty($p->api_key)) {
                $this->error("Lewati provider #{$p->id} ({$p->name}) — base_url/api_key kosong.");
                continue;
            }

            $this->info("Mengambil daftar layanan dari {$p->name} ({$p->base_url})...");

            try {
                $client = new \App\Services\Smm\JapClient(
                    baseUrl: $p->base_url,
                    apiKey: $p->api_key,
                    providerId: $p->id ?: null
                );

                $services = $client->services(); // array items dari JAP
                $created = 0;
                $updated = 0;
                $seenKeys = [];

                DB::transaction(function () use ($services, $p, &$created, &$updated, &$seenKeys) {
                    foreach ($services as $item) {
                        // Normalisasi minimal field JAP
                        $extId   = (string) ($item['service'] ?? $item['id'] ?? '');
                        $name    = (string) ($item['name'] ?? 'Unknown');
                        $rate    = (float)  ($item['rate'] ?? 0);
                        $min     = (int)    ($item['min'] ?? 0);
                        $max     = (int)    ($item['max'] ?? 0);
                        $catName = (string) ($item['category'] ?? 'Uncategorized');
                        $active  = true;

                        if ($extId === '' || $rate <= 0) {
                            continue; // lewati baris tak valid
                        }

                        // Pastikan kategori ada (scoped per provider optional)
                        $category = Category::firstOrCreate(
                            ['name' => $catName],
                            ['provider_id' => ($p->id ?: null), 'active' => true]
                        );

                        // Kunci unik: provider_id + external_service_id
                        $key = ($p->id ?: 0) . '::' . $extId;
                        $seenKeys[] = $key;

                        $svc = Service::where('provider_id', $p->id ?: null)
                            ->where('external_service_id', $extId)
                            ->first();

                        $payload = [
                            'name'        => $name,
                            'description' => $item['description'] ?? null,
                            'rate'        => $rate,     // USD per 1000 dari JAP
                            'min'         => $min,
                            'max'         => $max,
                            'category_id' => $category->id,
                            'active'      => $active,
                        ];

                        if ($svc) {
                            $svc->fill($payload)->save();
                            $updated++;
                        } else {
                            Service::create($payload + [
                                'provider_id'         => $p->id ?: null,
                                'external_service_id' => $extId,
                                // field publik tetap null default; admin akan mengisi belakangan
                            ]);
                            $created++;
                        }
                    }
                });

                $deactivated = 0;
                if ($this->option('deactivate-missing')) {
                    // Nonaktifkan service yang tidak terlihat di fetch terbaru
                    $this->info("Menonaktifkan layanan yang hilang di sumber {$p->name}...");
                    $extIdsSeen = collect($seenKeys)
                        ->filter(fn($k) => str_starts_with($k, ($p->id ?: 0) . '::'))
                        ->map(fn($k) => explode('::', $k, 2)[1])
                        ->values()
                        ->all();

                    $deactivated = Service::where('provider_id', $p->id ?: null)
                        ->when(!empty($extIdsSeen), fn($q) => $q->whereNotIn('external_service_id', $extIdsSeen))
                        ->update(['active' => false]);
                }

                $cats = Service::where('provider_id', $p->id ?: null)
                    ->where('active', true)
                    ->distinct()->count('category_id');

                $totalActive = Service::where('provider_id', $p->id ?: null)
                    ->where('active', true)
                    ->count();

                $this->info("{$p->name}: Selesai. Created: {$created}, Updated: {$updated}, Deactivated: {$deactivated}");
                $this->line("Kategori unik: {$cats}");
                $this->line("Total layanan aktif provider {$p->name}: {$totalActive}");
            } catch (\Throwable $e) {
                $this->error("Gagal sync untuk {$p->name}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
