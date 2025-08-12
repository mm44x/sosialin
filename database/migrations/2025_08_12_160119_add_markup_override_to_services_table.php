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
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('markup_percent_override', 5, 2)->nullable()->after('rate');
        });
    }
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('markup_percent_override');
        });
    }
};
