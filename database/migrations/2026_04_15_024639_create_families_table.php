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
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('p_b_dewasa_id')
             ->constrained('p_b_dewasas')
             ->cascadeOnDelete();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama');
            $table->string('pendidikan_terakhir');
            $table->string('pekerjaan');
            $table->text('alamat');

            // Ayah, Ibu, Saudara Kandung, Suami, Istri
            $table->enum('hubungan_keluarga', [
                'Ayah Kandung',
                'Ibu Kandung',
                'Saudara Kandung',
                'Suami',
                'Istri'
            ]);
            $table->integer('usia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
