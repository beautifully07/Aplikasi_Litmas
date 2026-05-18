<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\family;
use App\Models\Guarantor;
use App\Models\Pasal;
use App\Models\PBDewasa;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class ExportController extends Controller
{
    // =========================================================================
    // STORE
    // =========================================================================

    public function store(Request $request)
    {
        $request->validate([
            'client_id'    => 'required',
            'guarantor_id' => 'required',
            'perkara'      => 'required',
            'pasal_ids'    => 'nullable|array',
            'pasal_ids.*'  => 'exists:pasals,id',
        ]);

        $client = Client::findOrFail($request->client_id);

        $litmas = PBDewasa::create([
            // ── RELASI ──────────────────────────────────────────────────────
            'client_id'    => $request->client_id,
            'user_id'      => $client->user_id,
            'guarantor_id' => $request->guarantor_id,

            // ── STEP 1 : NOTA DINAS ─────────────────────────────────────────
            'no_nota_dinas'      => $request->no_nota_dinas,
            'tanggal_nota_dinas' => $request->tgl_nota_dinas,
            'asal_surat_rujukan' => $request->asal_surat_rujukan,
            'no_surat_rujukan'   => $request->no_surat,
            'tgl_surat_rujukan'  => $request->tgl_surat_rujukan,
            'no_reg_rutan'       => $request->no_reg_rutan,
            'perkara'            => $request->perkara,

            // ── STEP 2 : PK & IDENTITAS ─────────────────────────────────────
            'nip'                        => $request->nip,
            'jabatan'                    => $request->jabatan,
            'tanggal_studi_literatur'    => $request->tanggal_wawancara,
            'saksi'                      => $request->sumber_informasi,
            'no_putusan_pengadilan'      => $request->no_putusan_pengadilan,
            'tanggal_putusan_pengadilan' => $request->tgl_putusan_pengadilan,
            'lama_pidana_denda'          => $request->lama_pidana,
            'no_litmas'                  => $request->no_litmas,

            // ── STEP 2 : KONDISI PENJAMIN ───────────────────────────────────
            'perkawinan_penjamin'        => $request->penjamin_perkawinan,
            'relasi_keluarga_penjamin'   => $request->penjamin_relasi_keluarga,
            'relasi_masyarakat_penjamin' => $request->penjamin_relasi_masyarakat,
            'pekerjaan_penjamin'         => $request->penjamin_pekerjaan,
            'kondisi_rumah_penjamin'     => $request->penjamin_rumah,

            // ── STEP 3 : A. RIWAYAT KELAHIRAN ───────────────────────────────
            'riwayat_kelahiran'    => $request->riwayat_kelahiran,
            'riwayat_pertumbuhan'  => $request->riwayat_pertumbuhan,
            'riwayat_perkembangan' => $request->riwayat_perkembangan,

            // ── STEP 3 : B. PENDIDIKAN ──────────────────────────────────────
            'pendidikan_keluarga'  => $request->pendidikan_keluarga,
            'pendidikan_formal'    => $request->pendidikan_formal,
            'pendidikan_nonformal' => $request->pendidikan_nonformal,

            // ── STEP 3 : C. TINGKAH LAKU ────────────────────────────────────
            'bakat_potensi'       => $request->bakat_potensi,
            'relasi_keluarga'     => $request->relasi_sosial,
            'ketaatan_agama'      => $request->ketaatan_agama,
            'kebiasaan_baik'      => $request->kebiasaan_baik,
            'kebiasaan_buruk'     => $request->kebiasaan_buruk,
            'sikap_bekerja'       => $request->sikap_kerja,
            'riwayat_pelanggaran' => $request->riwayat_hukum,
            'riwayat_napza'       => $request->riwayat_zat,

            // ── STEP 3 : D. PERKAWINAN ──────────────────────────────────────
            'riwayat_perkawinan' => $request->riwayat_perkawinan,

            // ── STEP 3 : III. KONDISI LINGKUNGAN ────────────────────────────
            'relasi_masyarakat_klien'       => $request->lingkungan_relasi,
            'kondisi_lingkungan_klien'      => $request->lingkungan_kondisi,
            'profesi_masyarakat'            => $request->lingkungan_profesi,
            'ekonomi_masyarakat'            => $request->lingkungan_strata,
            'tingkat_pendidikan_masyarakat' => $request->lingkungan_pendidikan,
            'kehidupan_masyarakat'          => $request->kepedulian_masyarakat,
            'kegiatan_pendidikan'           => $request->kepedulian_pendidikan,
            'kegiatan_keagamaan'            => $request->kepedulian_agama,
            'penegak_hukum'                 => $request->kepedulian_hukum,

            // ── STEP 3 : IV. RIWAYAT TINDAK PIDANA ─────────────────────────
            'latar_belakang'    => $request->pidana_latar,
            'kronologis'        => $request->pidana_kronologis,
            'keadaan_korban'    => $request->pidana_korban,
            'dampak_klien'      => $request->akibat_klien,
            'dampak_keluarga'   => $request->akibat_keluarga,
            'dampak_masyarakat' => $request->akibat_masyarakat,

            // ── STEP 3 : V. TANGGAPAN ───────────────────────────────────────
            'tanggapan_klien'      => $request->tanggapan_klien,
            'tanggapan_keluarga'   => $request->tanggapan_keluarga,
            'tanggapan_masyarakat' => $request->tanggapan_masyarakat,
            'tanggapan_pemerintah' => $request->tanggapan_pemerintah,

            // ── STEP 3 : VI. EVALUASI PEMBINAAN ─────────────────────────────
            'program_admisi'      => $request->evaluasi_admisi,
            'sepertiga_pidana'    => $request->sepertiga_pidana,
            'seperdua_pidana'     => $request->seperdua_pidana,
            'duapertiga_pidana'   => $request->duapertiga_pidana,
            'program_kepribadian' => $request->pembinaan_kepribadian,
            'program_kemandirian' => $request->pembinaan_kemandirian,
            'warga_binaan'        => $request->relasi_wbp,
            'petugas'             => $request->relasi_petugas,
            'keluarga'            => $request->relasi_keluarga,
            'masyarakat'          => $request->relasi_masyarakat,

            // ── STEP 3 : VII. HASIL ASESMEN ─────────────────────────────────
            'rekomendasi_asesmen' => $request->hasil_asesmen,

            // ── STEP 3 : VIII. ANALISIS ─────────────────────────────────────
            'sikap_klien_pembinaan' => $request->analisis_resiko,
            'hasil_setelah_program' => $request->analisis_hasil,
            'kesiapan_masyarakat'   => $request->analisis_penerimaan,

            // ── STEP 3 : IX. KESIMPULAN ─────────────────────────────────────
            'tgl_rekomendasi' => $request->tgl_rekomendasi,
            'kesimpulan'      => $request->kesimpulan,
            'rekomendasi'     => $request->rekomendasi,
        ]);

        // ── SIMPAN KLASIFIKASI HUKUM + PASAL + AYAT ─────────────────────────
        if ($request->filled('pasal_ids')) {
            $this->syncPasalKlasifikasi($litmas, $request->pasal_ids);
        }

        // ── SIMPAN DATA KELUARGA ─────────────────────────────────────────────
        if ($request->filled('nama')) {
            foreach ($request->nama as $i => $nama) {
                if (!$nama) {
                    continue;
                }

                family::create([
                    'p_b_dewasa_id' => $litmas->id,
                    'client_id'     => $litmas->client_id,
                    'nama'          => $nama,
                    'jk'            => $request->jk[$i]        ?? null,
                    'usia'          => $request->usia[$i]       ?? null,
                    'pendidikan'    => $request->pendidikan[$i] ?? null,
                    'pekerjaan'     => $request->pekerjaan[$i]  ?? null,
                    'keterangan'    => $request->keterangan[$i] ?? null,
                    'no_kk'         => $request->no_kk          ?? null,
                ]);
            }
        }

        return redirect()->route('export.preview', $litmas->id)
            ->with('success', 'Data berhasil disimpan');
    }

    // =========================================================================
    // PRIVATE: SYNC PASAL + KLASIFIKASI + AYAT KE PIVOT
    // =========================================================================

    /**
     * Format pasal_ids dari form:
     *   "8"   → pasal id=8, tanpa ayat
     *   "8_3" → pasal id=8, ayat id=3
     */
    private function syncPasalKlasifikasi(PBDewasa $litmas, array $pasalIds): void
    {
        $pasalSync = [];
        $ayatSync  = [];

        foreach ($pasalIds as $value) {
            $parts   = explode('_', $value);
            $pasalId = (int) $parts[0];
            $ayatId  = isset($parts[1]) ? (int) $parts[1] : null;

            $pasalSync[] = $pasalId;

            if ($ayatId) {
                $ayatSync[] = $ayatId;
            }
        }

        // Ambil pasal beserta klasifikasi terkait
        $pasals = Pasal::with('klasifikasiHukum')
            ->whereIn('id', array_unique($pasalSync))
            ->get();

        // Kumpulkan ID klasifikasi unik dari pasal-pasal yang dipilih
        $klasifikasiIds = $pasals
            ->pluck('klasifikasi_hukum_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Sync ke semua pivot
        $litmas->klasifikasiHukum()->sync($klasifikasiIds);
        $litmas->pasals()->sync(array_unique($pasalSync));

        if (method_exists($litmas, 'ayats') && !empty($ayatSync)) {
            $litmas->ayats()->sync(array_unique($ayatSync));
        }
    }

    // =========================================================================
    // PRIVATE: GET DATA + EAGER-LOAD RELASI
    // =========================================================================

    private function getLitmasData(int|string $id): PBDewasa
    {
        return PBDewasa::with([
            'client',
            'user',
            'guarantor',
            'families',

            /*
             * Hierarki untuk formatPerkara:
             *
             *  klasifikasiHukum
             *    └── pasals   ← dibatasi hanya pasal yang di-pivot ke litmas ini
             *          └── ayats
             *
             * Tanpa constraint whereHas, klasifikasi akan memuat SEMUA pasal
             * miliknya di DB—bukan hanya yang dipilih user untuk litmas ini.
             */
            'klasifikasiHukum.pasals' => function ($query) use ($id) {
                $query->whereHas('pbDewasas', fn($q) => $q->where('p_b_dewasas.id', $id));
            },
            'klasifikasiHukum.pasals.ayats',
        ])->findOrFail($id);
    }

    // =========================================================================
    // PRIVATE: FORMAT PERKARA
    // =========================================================================

    /**
     * Menghasilkan string perkara lengkap.
     *
     * Contoh output:
     *   Narkotika / Pasal 112 ayat (1) dan Pasal 114 ayat (1) dan (2)
     *   UU RI No 35 Tahun 2009; Pasal 480 KUHP
     *
     * Aturan:
     *   • Ayat dalam 1 pasal   → "(1) dan (2)"
     *   • Pasal dalam 1 klsf   → "Pasal X ... dan Pasal Y ..."
     *   • Antar klasifikasi    → dipisah "; "
     *   • Format akhir         → "{perkara} / {dasar hukum}"
     */
    private function formatPerkara(PBDewasa $litmas): string
    {
        if (!$litmas->klasifikasiHukum || $litmas->klasifikasiHukum->isEmpty()) {
            return $litmas->perkara ?? '-';
        }

        $bagianKlasifikasi = [];

        foreach ($litmas->klasifikasiHukum as $klasifikasi) {

            $namaKlasifikasi = $klasifikasi->nama_klasifikasi ?? '';
            $bagianPasal     = [];

            foreach ($klasifikasi->pasals as $pasal) {

                $nomorPasal = $pasal->nomor_pasal ?? '?';
                $ayats      = $pasal->ayats ?? collect();

                if ($ayats->isNotEmpty()) {
                    // Kumpulkan nomor ayat, urutkan secara numerik
                    $nomorAyat = $ayats
                        ->pluck('nomor_ayat')
                        ->filter()
                        ->sortBy(fn($a) => (int) $a)
                        ->values()
                        ->toArray();

                    if (!empty($nomorAyat)) {
                        // Format tiap ayat → "(1)", "(2)", dst.
                        $ayatFormatted = array_map(fn($a) => "({$a})", $nomorAyat);
                        $ayatString    = $this->joinDengan($ayatFormatted);
                        $bagianPasal[] = "Pasal {$nomorPasal} ayat {$ayatString}";
                    } else {
                        $bagianPasal[] = "Pasal {$nomorPasal}";
                    }
                } else {
                    // Pasal tanpa ayat
                    $bagianPasal[] = "Pasal {$nomorPasal}";
                }
            }

            if (empty($bagianPasal)) {
                // Klasifikasi tanpa pasal → tampilkan nama klasifikasi saja
                $bagianKlasifikasi[] = $namaKlasifikasi;
            } else {
                $pasalString         = $this->joinDengan($bagianPasal);
                // Format: "Pasal X ayat (1) dan Pasal Y ayat (2) UU ..."
                $bagianKlasifikasi[] = trim("{$pasalString} {$namaKlasifikasi}");
            }
        }

        // Gabungkan antar klasifikasi dengan "; "
        $dasarHukum = implode('; ', $bagianKlasifikasi);

        return trim($litmas->perkara ?? '') . ' / ' . $dasarHukum;
    }

    /**
     * Menggabungkan array string dengan koma di antara dan "dan" sebelum elemen terakhir.
     *
     * ["(1)"]                  → "(1)"
     * ["(1)", "(2)"]           → "(1) dan (2)"
     * ["(1)", "(2)", "(3)"]    → "(1), (2) dan (3)"
     * ["Pasal 112 ...", "Pasal 114 ..."] → "Pasal 112 ..., dan Pasal 114 ..."
     */
    private function joinDengan(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        if (count($items) === 1) {
            return $items[0];
        }

        $last = array_pop($items);

        return implode(', ', $items) . ' dan ' . $last;
    }

    // =========================================================================
    // PRIVATE: HELPER KONVERSI JENIS KELAMIN
    // =========================================================================

    /**
     * Mengubah 'L' → 'Laki-laki', 'P' → 'Perempuan'.
     * Jika nilai lain, kembalikan nilai aslinya atau '-' jika null.
     */
    private function formatJenisKelamin(?string $jk): string
    {
        return match (strtoupper(trim($jk ?? ''))) {
            'L'     => 'Laki-laki',
            'P'     => 'Perempuan',
            default => $jk ?? '-',
        };
    }

    // =========================================================================
    // PRIVATE: FORMAT TANGGAL
    // =========================================================================

    /**
     * "YYYY-MM-DD" → "2 Januari 2026"
     */
    private function formatTanggalIndo(?string $tanggal): string
    {
        if (!$tanggal || $tanggal === '-') {
            return '-';
        }

        $bulan = [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ];

        $date = date_create($tanggal);

        if (!$date) {
            return '-';
        }

        return (int) date_format($date, 'd')
            . ' ' . ($bulan[(int) date_format($date, 'm')] ?? '-')
            . ' ' . date_format($date, 'Y');
    }

    /**
     * "YYYY-MM-DD" → "Senin, 2 Januari 2026"
     */
    private function formatTanggalHariIndo(?string $tanggal): string
    {
        if (!$tanggal || $tanggal === '-') {
            return '-';
        }

        $hariIndo = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];

        $date = date_create($tanggal);

        if (!$date) {
            return '-';
        }

        $hari = $hariIndo[date_format($date, 'l')] ?? '-';

        return $hari . ', ' . $this->formatTanggalIndo($tanggal);
    }

    // =========================================================================
    // PRIVATE: BUILD TEMPLATE DATA (SEMUA PLACEHOLDER)
    // =========================================================================

    private function buildTemplateData(PBDewasa $litmas, string $perkara): array
    {
        // Ambil semua guarantor klien untuk mencari data ayah & ibu
        $guarantors = Guarantor::where('client_id', $litmas->client_id)->get();

        $ayah = $guarantors->first(
            fn($g) => strtolower(trim($g->hubungan_keluarga)) === 'ayah kandung'
        );

        $ibu = $guarantors->first(
            fn($g) => strtolower(trim($g->hubungan_keluarga)) === 'ibu kandung'
        );

        return [

            // ── NOTA DINAS ────────────────────────────────────────────────
            'no_nota_dinas'      => $litmas->no_nota_dinas                                      ?? '-',
            'tgl_nota_dinas'     => $this->formatTanggalIndo($litmas->tanggal_nota_dinas)       ?? '-',
            'asal_surat_rujukan' => $litmas->asal_surat_rujukan                                 ?? '-',
            'no_surat_rujukan'   => $litmas->no_surat_rujukan                                   ?? '-',
            'tgl_rujukan'        => $this->formatTanggalIndo($litmas->tgl_surat_rujukan)        ?? '-',
            'no_reg_rutan'       => $litmas->no_reg_rutan                                       ?? '-',
            'tgl_wawancara'      => $this->formatTanggalIndo($litmas->tanggal_studi_literatur)  ?? '-',
            'sumber_informasi'   => $litmas->saksi                                              ?? '-',

            // ── CLIENT ────────────────────────────────────────────────────
            'nama_klien'        => $litmas->client->nama                                        ?? '-',
            'nama_klien_upper'  => strtoupper($litmas->client->nama                             ?? '-'),
            'ttl_klien'         => ($litmas->client->tempat_lahir                               ?? '-')
                                    . ', ' . $this->formatTanggalIndo($litmas->client->tanggal_lahir ?? ''),
            'tmpt_lahir'        => $litmas->client->tempat_lahir                                ?? '-',
            'alamat_klien'      => $litmas->client->alamat                                      ?? '-',
            'agama_klien'       => $litmas->client->agama                                       ?? '-',

            // FIX #3: Konversi L/P → Laki-laki/Perempuan untuk klien
            'jk_klien'          => $this->formatJenisKelamin($litmas->client->jenis_kelamin     ?? null),

            'perkawinan_klien'  => $litmas->client->status_perkawinan                           ?? '-',
            'pendidikan_klien'  => $litmas->client->pendidikan                                  ?? '-',
            'pekerjaan_klien'   => $litmas->client->pekerjaan                                   ?? '-',
            'suku'              => $litmas->client->suku                                        ?? '-',
            'bangsa'            => $litmas->client->kebangsaan                                  ?? '-',
            'kewarganegaraan'   => $litmas->client->kewarganegaraan                             ?? '-',
            'ciri_khusus_klien' => $litmas->client->ciri_khusus                                 ?? '-',

            // ── USER / PK ─────────────────────────────────────────────────
            'nama_user'       => $litmas->user->name                                            ?? '-',
            'nama_user_upper' => strtoupper($litmas->user->name                                 ?? '-'),
            'nip'             => $litmas->nip                                                    ?? '-',
            'jabatan'         => $litmas->jabatan                                                ?? '-',
            'jabatan_upper'   => strtoupper($litmas->jabatan                                    ?? '-'),

            // ── PENJAMIN (tunggal) ────────────────────────────────────────
            'nama_penjamin'   => $litmas->guarantor->nama                                       ?? '-',
            'alamat_penjamin' => $litmas->guarantor->alamat                                     ?? '-',

            // ── PERKARA ───────────────────────────────────────────────────
            'perkara'         => $perkara,
            'perkara_upper'   => strtoupper($perkara),

            // ── AYAH ──────────────────────────────────────────────────────
            'nama_ayah'       => $ayah->nama                                                    ?? '-',
            'ttl_ayah'        => ($ayah->tempat_lahir                                           ?? '-')
                                    . ', ' . $this->formatTanggalIndo($ayah->tanggal_lahir      ?? ''),
            'agama_ayah'      => $ayah->agama                                                   ?? '-',

            // FIX #1: Pisahkan suku dan kewarganegaraan ayah menjadi 2 placeholder terpisah
            'suku_ayah'       => $ayah->suku                                                    ?? '-',
            'wn_ayah'         => $ayah->kewarganegaraan                                         ?? '-',

            // Jika template masih butuh gabungan "Jawa / WNI", sediakan juga:
            'bangsa_ayah'     => ($ayah->suku ?? '-') . ' / ' . ($ayah->kewarganegaraan         ?? '-'),

            'pendidikan_ayah' => $ayah->pendidikan_terakhir                                     ?? '-',
            'pekerjaan_ayah'  => $ayah->pekerjaan                                               ?? '-',
            'alamat_ayah'     => $ayah->alamat                                                  ?? '-',
            'hub_kel_ayah'    => $ayah->hubungan_keluarga                                       ?? '-',

            // ── IBU ───────────────────────────────────────────────────────
            'nama_ibu'        => $ibu->nama                                                     ?? '-',
            'ttl_ibu'         => ($ibu->tempat_lahir                                            ?? '-')
                                    . ', ' . $this->formatTanggalIndo($ibu->tanggal_lahir       ?? ''),
            'agama_ibu'       => $ibu->agama                                                    ?? '-',

            // FIX #1: Pisahkan suku dan kewarganegaraan ibu menjadi 2 placeholder terpisah
            'suku_ibu'        => $ibu->suku                                                     ?? '-',
            'wn_ibu'          => $ibu->kewarganegaraan                                          ?? '-',

            // Jika template masih butuh gabungan "Jawa / WNI", sediakan juga:
            'bangsa_ibu'      => ($ibu->suku ?? '-') . ' / ' . ($ibu->kewarganegaraan           ?? '-'),

            'pendidikan_ibu'  => $ibu->pendidikan_terakhir                                      ?? '-',
            'pekerjaan_ibu'   => $ibu->pekerjaan                                                ?? '-',
            'alamat_ibu'      => $ibu->alamat                                                   ?? '-',
            'hub_kel_ibu'     => $ibu->hubungan_keluarga                                        ?? '-',

            // ── KELUARGA ─────────────────────────────────────────────────
            'no_kk'           => $litmas->families->first()->no_kk                              ?? '-',

            // ── DATA UMUM ─────────────────────────────────────────────────
            'no_litmas'       => $litmas->no_litmas                                             ?? '-',
            'no_putusan'      => $litmas->no_putusan_pengadilan                                 ?? '-',
            'tgl_putusan'     => $this->formatTanggalIndo($litmas->tanggal_putusan_pengadilan)  ?? '-',
            'lama_pidana'     => $litmas->lama_pidana_denda                                     ?? '-',

            // ── RIWAYAT KELAHIRAN ─────────────────────────────────────────
            'kelahiran_klien'    => $litmas->riwayat_kelahiran                                  ?? '-',
            'pertumbuhan_klien'  => $litmas->riwayat_pertumbuhan                                ?? '-',
            'perkembangan_klien' => $litmas->riwayat_perkembangan                               ?? '-',

            // ── RIWAYAT PENDIDIKAN ────────────────────────────────────────
            'pendd_keluarga'  => $litmas->pendidikan_keluarga                                   ?? '-',
            'pendd_formal'    => $litmas->pendidikan_formal                                     ?? '-',
            'pendd_nonformal' => $litmas->pendidikan_nonformal                                  ?? '-',

            // ── TINGKAH LAKU ──────────────────────────────────────────────
            'bakat_klien'           => $litmas->bakat_potensi                                   ?? '-',
            'relasi_keluarga'       => $litmas->relasi_keluarga                                 ?? '-',
            'ketaatan_klien'        => $litmas->ketaatan_agama                                  ?? '-',
            'kebiasaan_baik_klien'  => $litmas->kebiasaan_baik                                  ?? '-',
            'kebiasaan_jelek_klien' => $litmas->kebiasaan_buruk                                 ?? '-',
            'sikap_bekerja'         => $litmas->sikap_bekerja                                   ?? '-',
            'pelanggaran_klien'     => $litmas->riwayat_pelanggaran                             ?? '-',
            'rokok_napza_alkohol'   => $litmas->riwayat_napza                                   ?? '-',

            // ── RIWAYAT PERKAWINAN ────────────────────────────────────────
            'rwyt_kawin_klien' => $litmas->riwayat_perkawinan                                   ?? '-',

            // ── KONDISI PENJAMIN ──────────────────────────────────────────
            'kawin_penjamin'     => $litmas->perkawinan_penjamin                                ?? '-',
            'rs_kel'             => $litmas->relasi_keluarga_penjamin                           ?? '-',
            'rs_mas'             => $litmas->relasi_masyarakat_penjamin                         ?? '-',
            'pekerjaan_penjamin' => $litmas->pekerjaan_penjamin                                 ?? '-',
            'rt_penjamin'        => $litmas->kondisi_rumah_penjamin                             ?? '-',

            // ── KONDISI LINGKUNGAN ────────────────────────────────────────
            'rs_mas_klien'         => $litmas->relasi_masyarakat_klien                          ?? '-',
            'eko_budaya_pen_klien' => $litmas->kondisi_lingkungan_klien                         ?? '-',
            'profesi_mas'          => $litmas->profesi_masyarakat                               ?? '-',
            'ekonomi_mas'          => $litmas->ekonomi_masyarakat                               ?? '-',
            'tingkat_pendd_mas'    => $litmas->tingkat_pendidikan_masyarakat                    ?? '-',

            // ── INTERAKSI SOSIAL ──────────────────────────────────────────
            'ph_mas'         => $litmas->kehidupan_masyarakat                                   ?? '-',
            'ph_pend'        => $litmas->kegiatan_pendidikan                                    ?? '-',
            'ph_agama'       => $litmas->kegiatan_keagamaan                                     ?? '-',
            'ph_hukum_norma' => $litmas->penegak_hukum                                          ?? '-',

            // ── RIWAYAT TINDAK PIDANA ─────────────────────────────────────
            'latar_blkg'     => $litmas->latar_belakang                                         ?? '-',
            'kronologis'     => $litmas->kronologis                                             ?? '-',
            'keadaan_korban' => $litmas->keadaan_korban                                         ?? '-',

            // ── AKIBAT ───────────────────────────────────────────────────
            'akibat_klien' => $litmas->dampak_klien                                             ?? '-',
            'akibat_kel'   => $litmas->dampak_keluarga                                          ?? '-',
            'akibat_mas'   => $litmas->dampak_masyarakat                                        ?? '-',

            // ── TANGGAPAN ─────────────────────────────────────────────────
            'tanggapan_klien'      => $litmas->tanggapan_klien                                  ?? '-',
            'tanggapan_kel'        => $litmas->tanggapan_keluarga                               ?? '-',
            'tanggapan_mas'        => $litmas->tanggapan_masyarakat                             ?? '-',
            'tanggapan_pemerintah' => $litmas->tanggapan_pemerintah                             ?? '-',

            // ── EVALUASI PEMBINAAN ────────────────────────────────────────
            'program_admisi'    => $litmas->program_admisi                                      ?? '-',
            'sepertiga_pidana'  => $this->formatTanggalIndo($litmas->sepertiga_pidana)          ?? '-',
            'seperdua_pidana'   => $this->formatTanggalIndo($litmas->seperdua_pidana)           ?? '-',
            'duapertiga_pidana' => $this->formatTanggalIndo($litmas->duapertiga_pidana)         ?? '-',

            // ── PROGRAM PEMBINAAN ─────────────────────────────────────────
            'prog_kepribadian' => $litmas->program_kepribadian                                  ?? '-',
            'prog_kemandirian' => $litmas->program_kemandirian                                  ?? '-',

            // ── RELASI SOSIAL DI RUTAN ────────────────────────────────────
            'rutan_wbm'     => $litmas->warga_binaan                                            ?? '-',
            'rutan_petugas' => $litmas->petugas                                                 ?? '-',
            'rutan_kel'     => $litmas->keluarga                                                ?? '-',
            'rutan_mas'     => $litmas->masyarakat                                              ?? '-',

            // ── HASIL ASESMEN ─────────────────────────────────────────────
            'hasil'         => $litmas->rekomendasi_asesmen                                     ?? '-',

            // ── ANALISIS ─────────────────────────────────────────────────
            'sikap_klien'       => $litmas->sikap_klien_pembinaan                               ?? '-',
            'hasil_prog_binaan' => $litmas->hasil_setelah_program                               ?? '-',
            'penerimaan_mas'    => $litmas->kesiapan_masyarakat                                 ?? '-',

            // ── KESIMPULAN & REKOMENDASI ──────────────────────────────────
            'kesimpulan'      => $litmas->kesimpulan                                            ?? '-',
            'tgl_rekomendasi' => $this->formatTanggalHariIndo($litmas->tgl_rekomendasi)        ?? '-',
            'rekomendasi'     => $litmas->rekomendasi                                           ?? '-',
        ];
    }

    // =========================================================================
    // EXPORT WORD
    // =========================================================================

    public function exportWord(int|string $id)
    {
        $litmas  = $this->getLitmasData($id);
        $perkara = $this->formatPerkara($litmas);

        // ── Validasi template ada ─────────────────────────────────────────
        $templatePath = storage_path('app/public/templates/litmas/litmas.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Template dokumen tidak ditemukan. Hubungi administrator.');
        }

        $template = new TemplateProcessor($templatePath);

        // ── Set semua placeholder (karakter XML di-escape agar tidak corrupt)
        $data = $this->buildTemplateData($litmas, $perkara);

        foreach ($data as $key => $value) {
            $template->setValue(
                $key,
                htmlspecialchars((string) $value, ENT_XML1, 'UTF-8')
            );
        }

        // ── Tabel keluarga ────────────────────────────────────────────────
        if ($litmas->families->isNotEmpty()) {
            $template->cloneRow('nama', $litmas->families->count());

            foreach ($litmas->families as $i => $f) {
                $index = $i + 1;

                // FIX #2: Isi kolom nomor urut (pastikan template pakai ${no})
                $template->setValue("no#$index", (string) $index);

                // FIX #3: Konversi L/P di tabel keluarga juga
                $jkLabel = $this->formatJenisKelamin($f->jk ?? null);

                $template->setValue("nama#$index",       htmlspecialchars($f->nama        ?? '-', ENT_XML1, 'UTF-8'));
                $template->setValue("jk#$index",         htmlspecialchars($jkLabel,                ENT_XML1, 'UTF-8'));
                $template->setValue("usia#$index",       htmlspecialchars(($f->usia       ?? '-') . ' tahun', ENT_XML1, 'UTF-8'));
                $template->setValue("pendidikan#$index", htmlspecialchars($f->pendidikan  ?? '-', ENT_XML1, 'UTF-8'));
                $template->setValue("pekerjaan#$index",  htmlspecialchars($f->pekerjaan   ?? '-', ENT_XML1, 'UTF-8'));
                $template->setValue("ket#$index",        htmlspecialchars($f->keterangan  ?? '-', ENT_XML1, 'UTF-8'));
            }
        } else {
            // FIX #2: Isi juga placeholder 'no' saat tidak ada data keluarga
            $template->setValue('no',         '-');
            $template->setValue('nama',       '-');
            $template->setValue('jk',         '-');
            $template->setValue('usia',        '-');
            $template->setValue('pendidikan',  '-');
            $template->setValue('pekerjaan',   '-');
            $template->setValue('ket',         '-');
        }

        // ── Simpan file sementara ─────────────────────────────────────────
        $namaKlien = $litmas->client->nama ?? 'dokumen';
        $fileName  = 'litmas_' . $litmas->id . '_' . time() . '.docx';
        $path      = storage_path('app/' . $fileName);

        try {
            $template->saveAs($path);
        } catch (\Throwable $e) {
            if (file_exists($path)) {
                @unlink($path);
            }
            abort(500, 'Gagal membuat dokumen: ' . $e->getMessage());
        }

        return response()
            ->download($path, 'Litmas_' . $namaKlien . '.docx')
            ->deleteFileAfterSend(true);
    }

    // =========================================================================
    // PREVIEW
    // =========================================================================

    public function preview(int|string $id)
    {
        $litmas  = $this->getLitmasData($id);
        $perkara = $this->formatPerkara($litmas);

        return view('litmas.preview', compact('litmas', 'perkara'));
    }
}