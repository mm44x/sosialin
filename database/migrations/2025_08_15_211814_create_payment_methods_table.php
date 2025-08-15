<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            // jenis: bank | qris | ewallet | other
            $table->string('type', 20)->default('bank');
            // nama tampil (mis. "BCA • 1234567890 a.n PT Sosialin" atau "QRIS")
            $table->string('name', 150);
            // detail optional (untuk type=bank biasanya terpakai)
            $table->string('bank_name', 80)->nullable();
            $table->string('account_name', 120)->nullable();
            $table->string('account_number', 80)->nullable();

            // instruksi HTML/teks (optional)
            $table->text('instructions')->nullable();

            // media (gambar QR / dsb) disimpan path disk 'public'
            $table->string('media_path')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
