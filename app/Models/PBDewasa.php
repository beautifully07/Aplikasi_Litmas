<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PBDewasa extends Model
{
    use HasFactory;

    protected $table = 'PBDewasa';

    protected $fillable = [

    // ================================
    // DATA UTAMA LITMAS
    // ================================
    'no_litmas',
    'tanggal_litmas',
    'nip',
    'jabatan',
    'perkara',

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
    'ekonomi_masyarakat',
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
    // TANGGAPAN KLIEN, KELUARGA, KORBAN
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
    '1/3_pidana',
    '1/2_pidana',
    '2/3_pidana',

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
    'rekomendasi',

];

public function client()
    {
        return $this->belongsTo(Client::class);
    }

public function user()
    {
        return $this->belongsTo(User::class);
    }

public function guarantor()
    {
        return $this->belongsTo(Guarantor::class);
    }

public function klasifikasiHukum()
    {
        return $this->belongsTo(KlasifikasiHukum::class);
    }
}
