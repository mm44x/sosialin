<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom role jika belum ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('user')->after('password');
            }

            // Tambah soft deletes jika belum ada
            if (!Schema::hasColumn('users', 'deleted_at')) {
                // taruh setelah remember_token (opsional, hanya untuk rapi)
                $table->softDeletes()->after('remember_token');
            }
        });

        // Backfill role untuk baris lama yang masih NULL (pastikan semua jadi 'user')
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->whereNull('role')->update(['role' => 'user']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
