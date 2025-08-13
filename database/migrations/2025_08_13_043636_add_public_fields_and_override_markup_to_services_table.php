<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom hanya jika belum ada (idempotent)
        if (!Schema::hasColumn('services', 'public_active')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('public_active')->default(true)->after('active');
            });
        }

        if (!Schema::hasColumn('services', 'public_name')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('public_name')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('services', 'public_description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->text('public_description')->nullable()->after('description');
            });
        }

        if (!Schema::hasColumn('services', 'markup_percent_override')) {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('markup_percent_override', 8, 2)->nullable()->after('rate');
            });
        }
    }

    public function down(): void
    {
        // Drop kolom jika ada (agar aman saat rollback)
        if (Schema::hasColumn('services', 'public_active')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('public_active');
            });
        }

        if (Schema::hasColumn('services', 'public_name')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('public_name');
            });
        }

        if (Schema::hasColumn('services', 'public_description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('public_description');
            });
        }

        if (Schema::hasColumn('services', 'markup_percent_override')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('markup_percent_override');
            });
        }
    }
};
