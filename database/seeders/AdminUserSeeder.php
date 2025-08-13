<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@sosialin.local');
        $pass  = env('ADMIN_PASSWORD', 'secret12345');
        $seed  = (float) env('ADMIN_WALLET_SEED', 0);

        // Buat / ambil user admin
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make($pass),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Pastikan role & verifikasi benar jika user sudah ada
        if ($user->role !== 'admin' || is_null($user->email_verified_at)) {
            $user->role = 'admin';
            $user->email_verified_at = $user->email_verified_at ?: now();
            $user->save();
        }

        // Pastikan wallet ada
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        // Seed saldo awal (opsional, idempoten — hanya isi jika balance saat ini 0 dan seed > 0)
        if ((float)$wallet->balance === 0.0 && $seed > 0) {
            $wallet->balance = round($seed, 2);
            $wallet->save();

            Transaction::create([
                'user_id' => $user->id,
                'type'    => 'topup',
                'amount'  => round($seed, 2),
                'meta'    => ['reason' => 'admin_wallet_seed'],
            ]);
        }

        $this->command->info("Admin siap: {$email} (password diset dari .env)");
    }
}
