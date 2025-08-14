<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('api_logs', function (Blueprint $table) {
            // Index dasar untuk filter umum
            $table->index('provider_id', 'api_logs_provider_idx');
            $table->index('endpoint', 'api_logs_endpoint_idx');
            $table->index('status_code', 'api_logs_status_idx');
            $table->index('created_at', 'api_logs_created_at_idx');

            // Index gabungan untuk query (provider + endpoint + created_at)
            $table->index(['provider_id', 'endpoint', 'created_at'], 'api_logs_provider_endpoint_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('api_logs', function (Blueprint $table) {
            $table->dropIndex('api_logs_provider_idx');
            $table->dropIndex('api_logs_endpoint_idx');
            $table->dropIndex('api_logs_status_idx');
            $table->dropIndex('api_logs_created_at_idx');
            $table->dropIndex('api_logs_provider_endpoint_created_idx');
        });
    }
};
