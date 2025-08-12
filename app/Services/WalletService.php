<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class WalletService
{
    public function credit(int $userId, float $amount, string $type = 'topup', array $meta = []): void
    {
        if ($amount <= 0) throw new InvalidArgumentException('Amount harus > 0');

        DB::transaction(function () use ($userId, $amount, $type, $meta) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = Wallet::create(['user_id' => $userId, 'balance' => 0]);
                $wallet->refresh();
                $wallet->lockForUpdate();
            }

            $wallet->balance = round(((float)$wallet->balance) + $amount, 2);
            $wallet->save();

            Transaction::create([
                'user_id' => $userId,
                'type'    => $type,
                'amount'  => round($amount, 2),
                'meta'    => $meta,
            ]);
        });
    }

    public function debit(int $userId, float $amount, array $meta = []): void
    {
        if ($amount <= 0) throw new InvalidArgumentException('Amount harus > 0');

        DB::transaction(function () use ($userId, $amount, $meta) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            if (!$wallet) {
                throw new RuntimeException('Wallet tidak ditemukan untuk user.');
            }

            $newBalance = round(((float)$wallet->balance) - $amount, 2);
            if ($newBalance < 0) {
                throw new RuntimeException('Saldo tidak mencukupi.');
            }

            $wallet->balance = $newBalance;
            $wallet->save();

            Transaction::create([
                'user_id' => $userId,
                'type'    => 'order',
                'amount'  => round(-$amount, 2),
                'meta'    => $meta,
            ]);
        });
    }
}
