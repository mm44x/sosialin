<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;

class WalletEnsure extends Command
{
    protected $signature = 'wallet:ensure {--user_id=}';
    protected $description = 'Pastikan semua user (atau user tertentu) memiliki wallet';

    public function handle(): int
    {
        $uid = $this->option('user_id');

        $users = User::query()
            ->when($uid, fn($q) => $q->where('id', $uid))
            ->get();

        $created = 0;
        foreach ($users as $u) {
            $w = Wallet::firstOrCreate(['user_id' => $u->id], ['balance' => 0]);
            if ($w->wasRecentlyCreated) $created++;
        }

        $this->info("Wallet ensured. Total processed: {$users->count()}, created: {$created}");
        return self::SUCCESS;
    }
}
