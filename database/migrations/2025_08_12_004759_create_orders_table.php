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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('link');                         // target URL/username
            $table->unsignedInteger('quantity');
            $table->enum('status', ['pending', 'processing', 'completed', 'partial', 'canceled', 'error'])
                ->default('pending');
            $table->string('provider_order_id')->nullable();
            $table->decimal('cost', 12, 2)->default(0);     // biaya yang kita kenakan (setelah markup)
            $table->json('meta')->nullable();               // catatan/response provider
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('provider_order_id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
