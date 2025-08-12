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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['jap', 'custom', 'other'])->default('jap');
            $table->string('base_url');
            $table->string('api_key')->nullable();      // simpan terenkripsi nanti jika perlu
            $table->decimal('markup_percent', 5, 2)->default(0); // markup default %
            $table->boolean('active')->default(true);
            $table->json('meta')->nullable();           // opsi tambahan
            $table->timestamps();
            $table->unique(['name', 'base_url']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
