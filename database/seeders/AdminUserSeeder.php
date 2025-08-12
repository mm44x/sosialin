<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@sosialin.local';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin Sosialin',
                'password' => Hash::make('admin12345'), // ganti nanti di production
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        DB::table('wallets')->updateOrInsert(
            ['user_id' => $user->id],
            ['balance' => 0, 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
