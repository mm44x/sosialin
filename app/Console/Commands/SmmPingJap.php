<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Smm\JapClient;
use App\Models\Provider;

class SmmPingJap extends Command
{
    protected $signature = 'smm:ping-jap {--provider=JAP}';
    protected $description = 'Ping JAP provider via action=balance';

    public function handle(): int
    {
        $base = env('JAP_BASE_URL');
        $key  = env('JAP_API_KEY');

        if (!$base || !$key) {
            $this->error('ENV JAP_BASE_URL / JAP_API_KEY belum diisi.');
            return self::FAILURE;
        }

        $provider = Provider::where('name', $this->option('provider'))->first();
        $client = new JapClient($base, $key, $provider?->id);

        try {
            $res = $client->balance();
            $this->info('OK: response: ' . json_encode($res));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
