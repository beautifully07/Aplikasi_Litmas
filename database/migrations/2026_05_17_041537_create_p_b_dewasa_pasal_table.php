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
        Schema::create('p_b_dewasa_pasal', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('p_b_dewasa_id');
            $table->unsignedBigInteger('pasal_id');

            // Foreign key
            $table->foreign('p_b_dewasa_id')
                ->references('id')
                ->on('p_b_dewasas')
                ->onDelete('cascade');

            $table->foreign('pasal_id')
                ->references('id')
                ->on('pasals')
                ->onDelete('cascade');

            // Optional (biar tidak duplikat)
            $table->unique(['p_b_dewasa_id', 'pasal_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_b_dewasa_pasal');
    }
};
