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
        Schema::create('pb_dewasa_klasifikasi_hukum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p_b_dewasa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('klasifikasi_hukum_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pb_dewasa_klasifikasi_hukum');
    }
};
