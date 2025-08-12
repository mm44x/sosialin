<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Provider;
use App\Models\Category;
use App\Models\Service;
use App\Services\Smm\JapClient;

class SmmSyncServices extends Command
{
    protected $signature = 'smm:sync-services 
                            {--provider=JAP : Nama provider di tabel providers} 
                            {--deactivate-missing : Nonaktifkan layanan yang tidak ada di hasil terbaru}';
    protected $description = 'Sinkron daftar layanan dari provider (JAP) ke database lokal';

    public function handle(): int
    {
        $providerName = $this->option('provider');
        $provider = Provider::where('name', $providerName)->first();

        if (!$provider) {
            $this->error("Provider {$providerName} tidak ditemukan. Jalankan seeder atau buat via Admin.");
            return self::FAILURE;
        }
        $base = env('JAP_BASE_URL');
        $key  = env('JAP_API_KEY');
        if (!$base || !$key) {
            $this->error('ENV JAP_BASE_URL / JAP_API_KEY belum diisi.');
            return self::FAILURE;
        }

        $client = new JapClient($base, $key, $provider->id);

        $this->info("Mengambil daftar layanan dari {$provider->name} ({$base})...");
        try {
            $data = $client->services();
        } catch (\Throwable $e) {
            $this->error("Gagal mengambil services: " . $e->getMessage());
            return self::FAILURE;
        }

        // Beberapa panel mengembalikan array layanan langsung, sebagian bungkus dalam key.
        if (isset($data['services']) && is_array($data['services'])) {
            $items = $data['services'];
        } else {
            $items = $data; // asumsikan array of objects
        }
        if (!is_array($items)) {
            $this->error('Format response tidak dikenali (bukan array).');
            return self::FAILURE;
        }

        $updated = $created = 0;
        $seenExternalIds = [];

        foreach ($items as $it) {
            // Ambil nilai dengan fallback yang aman
            $extId = (string)($it['service'] ?? $it['id'] ?? '');
            if ($extId === '') continue;

            $name = (string)($it['name'] ?? 'Unnamed Service');
            $catName = trim((string)($it['category'] ?? 'Uncategorized'));
            $rate = (float)($it['rate'] ?? 0);
            $min = (int)($it['min'] ?? 0);
            $max = (int)($it['max'] ?? 0);
            $desc = isset($it['description']) ? (string)$it['description'] : null;

            $category = Category::firstOrCreate(
                ['name' => $catName, 'provider_id' => $provider->id],
                ['active' => true]
            );

            $payload = [
                'category_id'         => $category->id,
                'provider_id'         => $provider->id,
                'name'                => $name,
                'rate'                => $rate,  // rate per 1000 dari provider (markup dihitung saat order)
                'min'                 => $min,
                'max'                 => $max,
                'description'         => $desc,
                'active'              => true,
                'meta'                => null,
            ];

            $svc = Service::where('provider_id', $provider->id)
                ->where('external_service_id', $extId)
                ->first();

            if ($svc) {
                $svc->update($payload);
                $updated++;
            } else {
                Service::create(array_merge($payload, [
                    'external_service_id' => $extId,
                ]));
                $created++;
            }

            $seenExternalIds[] = $extId;
        }

        // Nonaktifkan layanan yang tidak ada di hasil terbaru (opsional)
        $deactivated = 0;
        if ($this->option('deactivate-missing')) {
            $deactivated = Service::where('provider_id', $provider->id)
                ->whereNotIn('external_service_id', $seenExternalIds)
                ->update(['active' => false]);
        }

        $this->info("Selesai. Created: {$created}, Updated: {$updated}" . ($this->option('deactivate-missing') ? ", Deactivated: {$deactivated}" : ''));
        $this->info("Kategori unik: " . Category::where('provider_id', $provider->id)->count());
        $this->info("Total layanan aktif provider {$provider->name}: " . Service::where('provider_id', $provider->id)->where('active', true)->count());

        return self::SUCCESS;
    }
}
