<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provider;

class JapProviderSeeder extends Seeder
{
    public function run(): void
    {
        Provider::firstOrCreate(
            ['name' => 'JAP', 'base_url' => config('app.jap_base_url', env('JAP_BASE_URL'))],
            ['type' => 'jap', 'markup_percent' => 0, 'active' => true]
        );
    }
}
