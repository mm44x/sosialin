<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('endpoint', 100);
            $table->json('request')->nullable();
            $table->integer('status_code')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->longText('response')->nullable();
            $table->timestamps();

            $table->index(['provider_id', 'endpoint', 'created_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
