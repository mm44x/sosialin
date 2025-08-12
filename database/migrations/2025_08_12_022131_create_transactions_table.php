<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['topup','order','refund']);
            $table->decimal('amount', 12, 2); // + untuk topup/refund, - untuk order
            $table->json('meta')->nullable(); // catatan tambahan (ref, order_id, dsb.)
            $table->timestamps();

            $table->index(['user_id','type','created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
