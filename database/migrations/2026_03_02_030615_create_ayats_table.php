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
        Schema::create('ayats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasal_id')
                ->constrained('pasals')
                ->onDelete('cascade');
            $table->string('nomor_ayat');
            $table->text('isi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayats');
    }
};
