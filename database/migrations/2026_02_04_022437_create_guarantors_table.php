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
        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
             // Relasi ke klien / warga binaan (opsional, sesuaikan kebutuhan)
            $table->unsignedBigInteger('client_id')->nullable();
            $table->integer('no_kk');
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama');
            $table->string('suku');
            $table->string('kewarganegaraan');
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

            // Optional foreign key
            // $table->foreign('klien_id')->references('id')->on('kliens')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'guarantors');
    }
};
