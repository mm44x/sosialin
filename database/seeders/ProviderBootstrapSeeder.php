<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        $base = env('JAP_BASE_URL');
        $key  = env('JAP_API_KEY');
        $mkp  = (float) env('PROVIDER_JAP_MARKUP', 20);

        // Buat / update provider bernama "JustAnotherPanel"
        $prov = Provider::firstOrCreate(
            ['name' => 'JustAnotherPanel'],
            [
                'type'           => 'jap',
                'base_url'       => $base,
                'api_key'        => $key,
                'markup_percent' => $mkp,
                'active'         => true,
            ]
        );

        // Jika sudah ada tapi kolom kosong, isi dari .env (tanpa menimpa nilai yang sudah terisi)
        $dirty = false;
        if (!$prov->base_url && $base) {
            $prov->base_url = $base;
            $dirty = true;
        }
        if (!$prov->api_key && $key) {
            $prov->api_key  = $key;
            $dirty = true;
        }
        if (is_null($prov->markup_percent)) {
            $prov->markup_percent = $mkp;
            $dirty = true;
        }
        if (!$prov->active) {
            $prov->active = true;
            $dirty = true;
        }
        if ($dirty) $prov->save();

        $this->command->info("Provider bootstrap: {$prov->name} (markup {$prov->markup_percent}%).");
    }
}
