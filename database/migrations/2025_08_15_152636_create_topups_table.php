<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 40)->unique();        // ex: TP20250814ABC1
            $table->string('method', 30)->nullable();         // bank|qris|other
            $table->decimal('amount', 14, 2);
            $table->string('status', 20)->default('pending'); // pending|approved|rejected
            $table->string('proof_path')->nullable();         // storage path to image/pdf
            $table->text('note')->nullable();                 // catatan user/admin
            $table->json('meta')->nullable();

            // Audit review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topups');
    }
};
