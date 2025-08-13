<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Provider;

class SmmPingJap extends Command
{
    protected $signature = 'smm:ping-jap {--provider=}';
    protected $description = 'Cek endpoint balance ke JAP untuk provider tertentu atau semua provider aktif';

    public function handle(): int
    {
        $opt = $this->option('provider');

        // Tentukan daftar provider yang akan dipakai
        $providers = collect();

        if ($opt) {
            // Bisa ID atau nama (case-insensitive)
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
            // Jika tidak ada di DB, pakai satu dari .env
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

            $this->info("Ping balance: {$p->name} ({$p->base_url}) ...");

            try {
                $client = new \App\Services\Smm\JapClient(
                    baseUrl: $p->base_url,
                    apiKey: $p->api_key,
                    providerId: $p->id ?: null
                );
                $resp = $client->balance();
                $this->line("OK #{$p->id} {$p->name}: " . json_encode($resp));
            } catch (\Throwable $e) {
                $this->error("Gagal #{$p->id} {$p->name}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
