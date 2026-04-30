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
        Schema::create('p_b_dewasas', function (Blueprint $table) {
            $table->id();

            // ================================
            // RELASI DATA (FOREIGN KEY)
            // ================================
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('guarantor_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('klasifikasi_hukums_id')->nullable();

            // ==============================
            // NOTA DINAS
            // ==============================
            $table->string('no_nota_dinas')->nullable();
            $table->string('tanggal_nota_dinas')->nullable();
            $table->text('asal_surat_rujukan')->nullable(); // Rutan Kelas IIIB
            $table->string('no_surat_rujukan')->nullable(); // WP.13.PAS
            $table->date('tgl_surat_rujukan')->nullable(); 
            $table->string('no_reg_rutan')->nullable(); // Bisa Rutan, Lapas

            // ===============================
            // COVER
            // ===============================
            $table->string('nip')->nullable(); // NIP Pembimbing
            $table->string('jabatan')->nullable(); // Jabatan Pembimbing
            
            // ================================
            // DATA UTAMA LITMAS
            // ================================
            $table->string('no_litmas')->nullable();
            $table->date('tanggal_litmas')->nullable();
            $table->text('perkara')->nullable();
            $table->string('no_putusan_pengadilan')->nullable();
            $table->date('tanggal_putusan_pengadilan')->nullable();
            $table->string('lama_pidana_denda')->nullable();
            

            // ================================
            // DATA KEGIATAN PENELITIAN
            // ================================
            $table->date('tanggal_studi_literatur')->nullable();
            $table->string('saksi')->nullable();

            // ================================
            // RIWAYAT HIDUP KLIEN
            // ================================
            $table->text('riwayat_kelahiran')->nullable();
            $table->text('riwayat_pertumbuhan')->nullable();
            $table->text('riwayat_perkembangan')->nullable();

            // =======================
            // RIWAYAT PENDIDIKAN
            // =======================
            $table->text('pendidikan_keluarga')->nullable();
            $table->text('pendidikan_formal')->nullable();
            $table->text('pendidikan_nonformal')->nullable();

            // ================================
            // RIWAYAT TINGKAH LAKU KLIEN
            // ================================
            $table->text('bakat_potensi')->nullable();
            $table->text('relasi_keluarga')->nullable();
            $table->text('ketaatan_agama')->nullable();
            $table->text('kebiasaan_baik')->nullable();
            $table->text('kebiasaan_buruk')->nullable();
            $table->text('sikap_bekerja')->nullable();
            $table->text('riwayat_pelanggaran')->nullable();
            $table->text('riwayat_napza')->nullable();
            $table->text('riwayat_perkawinan')->nullable();

            // ====================
            // KONDISI PENJAMIN
            // ====================
            $table->text('perkawinan_penjamin')->nullable();
            $table->text('relasi_keluarga_penjamin')->nullable();
            $table->text('relasi_masyarakat_penjamin')->nullable();
            $table->text('pekerjaan_penjamin')->nullable();
            $table->text('kondisi_rumah_penjamin')->nullable();

            // ===========================
            // KONDISI LINGKUNGAN KLIEN
            // ===========================
            $table->text('relasi_masyarakat_klien')->nullable();
            $table->text('kondisi_lingkungan_klien')->nullable();
            $table->text('profesi_masyarakat')->nullable();
            $table->text('eknomi_masyarakat')->nullable();
            $table->text('tingkat_pendidikan_masyarakat')->nullable();

            //==============================
            // INTERAKSI SOSIAL MASYARAKAT
            // =============================
            $table->text('kehidupan_masyarakat')->nullable();
            $table->text('kegiatan_pendidikan')->nullable();
            $table->text('kegiatan_keagamaan')->nullable();
            $table->text('penegak_hukum')->nullable();

            // =======================
            // RIWAYAT TINDAK PIDANA
            // =======================
            $table->text('latar_belakang')->nullable();
            $table->text('kronologis')->nullable();
            $table->text('keadaan_korban')->nullable();

            // ==================================
            // AKIBAT YANG BERDAMPAK PADA KLIEN, KELUARGA, DAN MASYARAKAT
            // ==================================
            $table->text('dampak_klien')->nullable();
            $table->text('dampak_keluarga')->nullable();
            $table->text('dampak_masyarakat')->nullable();

            // ===================================
            // TANGGAPAN KLIEN, KELUARGA, KORBAN, MASYARAKAT, DAN PEMERINTAH
            // ===================================
            $table->text('tanggapan_klien')->nullable();
            $table->text('tanggapan_keluarga')->nullable();
            $table->text('tanggapan_masyarakat')->nullable();
            $table->text('tanggapan_pemerintah')->nullable();

            // =========================
            // EVALUASI PERKEMBANGAN
            // =========================
            $table->text('program_admisi')->nullable();

            // =========================
            // TAHAPAN PEMBINAAN
            // ========================= 
            $table->date('1/3_pidana')->nullable();
            $table->date('1/2_pidana')->nullable();
            $table->date('2/3_pidana')->nullable();

            // ======================
            // PROGRAM PEMBINAAN
            // ======================
            $table->text('program_kepribadian')->nullable();
            $table->text('program_kemandirian')->nullable();

            // ======================
            // RELASI SOSIAL DI RUTAN
            // ======================
            $table->text('warga_binaan')->nullable();
            $table->text('petugas')->nullable();
            $table->text('keluarga')->nullable();
            $table->text('masyarakat')->nullable();

            // ===================
            // HASIL/REKOMENDASI ASESMEN
            // ===================
            $table->text('rekomendasi_asesmen')->nullable();

            // =====================
            // ANALISIS
            // =====================
            $table->text('sikap_klien_pembinaan')->nullable();
            $table->text('hasil_setelah_program')->nullable();
            $table->text('kesiapan_masyarakat')->nullable();

            // =========================
            // KESIMPULAN DAN REKOMENDASI
            // =========================
            $table->text('kesimpulan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_b_dewasas');
    }
};
