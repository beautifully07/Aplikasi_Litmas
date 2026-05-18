<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PBDewasa extends Model
{
    use HasFactory;

    protected $table = 'p_b_dewasas';

    protected $fillable = [

        'client_id',
        'user_id',
        'guarantor_id',
        
        // ================================
        // NOTA DINAS
        // ================================
        'no_nota_dinas',
        'tanggal_nota_dinas',
        'asal_surat_rujukan',
        'no_surat_rujukan',
        'tgl_surat_rujukan',
        'no_reg_rutan',

        // ================================
        // COVER
        // ================================
        'nip',
        'jabatan',

        // ================================
        // DATA UTAMA LITMAS
        // ================================
        'no_litmas',
        'tanggal_litmas',
        'perkara',
        'klasifikasi_hukums_id',
        'no_putusan_pengadilan',
        'tanggal_putusan_pengadilan',
        'lama_pidana_denda',

        // ================================
        // KEGIATAN PENELITIAN
        // ================================
        'tanggal_studi_literatur',
        'saksi',

        // ================================
        // RIWAYAT HIDUP KLIEN
        // ================================
        'riwayat_kelahiran',
        'riwayat_pertumbuhan',
        'riwayat_perkembangan',

        // ================================
        // RIWAYAT PENDIDIKAN
        // ================================
        'pendidikan_keluarga',
        'pendidikan_formal',
        'pendidikan_nonformal',

        // ================================
        // TINGKAH LAKU
        // ================================
        'bakat_potensi',
        'relasi_keluarga',
        'ketaatan_agama',
        'kebiasaan_baik',
        'kebiasaan_buruk',
        'sikap_bekerja',
        'riwayat_pelanggaran',
        'riwayat_napza',
        'riwayat_perkawinan',

        // ================================
        // PENJAMIN
        // ================================
        'perkawinan_penjamin',
        'relasi_keluarga_penjamin',
        'relasi_masyarakat_penjamin',
        'pekerjaan_penjamin',
        'kondisi_rumah_penjamin',

        // ================================
        // KONDISI LINGKUNGAN KLIEN
        // ================================
        'relasi_masyarakat_klien',
        'kondisi_lingkungan_klien',
        'profesi_masyarakat',
        'ekonomi_masyarakat',              // ✅ FIX: sesuai kolom di migration (typo 'eknomi' bukan 'ekonomi')
        'tingkat_pendidikan_masyarakat',

        // =================================
        // INTERAKSI SOSIAL MASYARAKAT
        // =================================
        'kehidupan_masyarakat',
        'kegiatan_pendidikan',
        'kegiatan_keagamaan',
        'penegak_hukum',

        // ================================
        // RIWAYAT TINDAK PIDANA
        // ================================
        'latar_belakang',
        'kronologis',
        'keadaan_korban',

        // ================================
        // AKIBAT YANG BERDAMPAK
        // ================================
        'dampak_klien',
        'dampak_keluarga',
        'dampak_masyarakat',

        // ================================
        // TANGGAPAN
        // ================================
        'tanggapan_klien',
        'tanggapan_keluarga',
        'tanggapan_masyarakat',
        'tanggapan_pemerintah',

        // ================================
        // EVALUASI PERKEMBANGAN
        // ================================
        'program_admisi',

        // ================================
        // TAHAP PEMBINAAN
        // ================================
        'sepertiga_pidana',
        'seperdua_pidana',
        'duapertiga_pidana',

        // ================================
        // PROGRAM PEMBINAAN
        // ================================
        'program_kepribadian',
        'program_kemandirian',

        // ================================
        // RELASI SOSIAL DI RUTAN
        // ================================
        'warga_binaan',
        'petugas',
        'keluarga',
        'masyarakat',

        // ================================
        // HASIL/REKOMENDASI ASESMEN
        // ================================
        'rekomendasi_asesmen',

        // ================================
        // ANALISIS
        // ================================
        'sikap_klien_pembinaan',
        'hasil_setelah_program',
        'kesiapan_masyarakat',

        // ================================
        // KESIMPULAN DAN REKOMENDASI
        // ================================
        'kesimpulan',
        'tgl_rekomendasi',
        'rekomendasi',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Penjamin tunggal (untuk $litmas->guarantor->nama)
     * Diambil dari kolom guarantor_id di tabel p_b_dewasas
     */
    public function guarantor()
    {
        return $this->belongsTo(Guarantor::class, 'guarantor_id');
    }

    /**
     * Semua penjamin terkait klien ini (hasMany)
     * Digunakan untuk dropdown ayah/ibu di form
     */
    public function guarantors()
    {
        return $this->hasMany(Guarantor::class, 'p_b_dewasa_id');
    }

    /**
     * Relasi klasifikasi hukum (pivot)
     *
     * ✅ FIX: pivot FK harus eksplisit karena nama tabel 'p_b_dewasas'
     * menyebabkan Laravel auto-generate FK yang salah ('p_b_dewasa_id'),
     * sedangkan kolom di tabel pivot adalah 'pb_dewasa_id'.
     *
     * Sesuaikan 'pb_dewasa_id' jika nama kolom pivot Anda berbeda.
     * Cek dengan: DB::select('DESCRIBE pb_dewasa_klasifikasi_hukum');
     */
    public function klasifikasiHukum()
    {
        return $this->belongsToMany(
            KlasifikasiHukum::class,
            'pb_dewasa_klasifikasi_hukum', // tabel pivot
            'p_b_dewasa_id',                // FK → p_b_dewasas.id  ← sesuaikan jika perlu
            'klasifikasi_hukum_id'         // FK → klasifikasi_hukums.id
        );
    }

    public function families()
    {
        return $this->hasMany(family::class, 'p_b_dewasa_id');
    }

 public function pasals()
{
    return $this->belongsToMany(Pasal::class, 'p_b_dewasa_pasal');
}

// public function ayats()
// {
//     return $this->belongsToMany(Ayat::class, 'p_b_dewasa_ayat');
// }
}