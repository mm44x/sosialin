<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('topups', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('user_id')
                  ->constrained('payment_methods')->nullOnDelete();
            // kolom 'method' string lama dibiarkan sebagai label yang disalin di saat submit
        });
    }

    public function down(): void
    {
        Schema::table('topups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
        });
    }
};