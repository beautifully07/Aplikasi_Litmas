<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasals', function (Blueprint $table) {

            $table->foreignId('klasifikasi_hukum_id')
                  ->nullable()
                  ->constrained('klasifikasi_hukums')
                  ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('pasals', function (Blueprint $table) {

            $table->dropForeign(['klasifikasi_hukum_id']);
            $table->dropColumn('klasifikasi_hukum_id');

        });
    }
};