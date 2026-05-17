<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\family;
use App\Models\PBDewasa;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class ExportController extends Controller
{

    public function store(Request $request)
{
    $request->validate([
        'client_id' => 'required',
        'guarantor_id' => 'required',
        'perkara' => 'required',
        'pasal_ids'   => 'nullable|array',   // ← tambah ini
        'pasal_ids.*' => 'exists:pasals,id', // ← validasi tiap ID
    ]);

    $client = Client::findOrFail($request->client_id);

    $litmas = PBDewasa::create([
        // ── RELASI ──
        'client_id' => $request->client_id,
        'user_id' => $client->user_id,
        'guarantor_id' => $request->guarantor_id,
        

        // ── STEP 1 : NOTA DINAS ─────────────────────────────────────
        'no_nota_dinas'      => $request->no_nota_dinas,   // name="no_nota_dinas"
        'tanggal_nota_dinas' => $request->tgl_nota_dinas,  // name="tgl_nota_dinas"
        'asal_surat_rujukan' => $request->asal_surat_rujukan,          
        'no_surat_rujukan'   => $request->no_surat,        // name="no_surat"
        'tgl_surat_rujukan'  => $request->tgl_surat_rujukan,
        'no_reg_rutan' => $request->no_reg_rutan,   // name="tanggal_surat" 'no_reg_rutan'=> $request->no_register, // name="no_register"
        'perkara' => $request->perkara,

        // ── STEP 2 : PK & IDENTITAS ─────────────────────────────────
            'nip'                         => $request->nip,                    // name="nip"
            'jabatan'                     => $request->jabatan,                // name="jabatan"
            'tanggal_studi_literatur'     => $request->tanggal_wawancara,     // name="tanggal_wawancara"
            'saksi'                       => $request->sumber_informasi,       // name="sumber_informasi"
            'no_putusan_pengadilan'       => $request->no_putusan_pengadilan, // name="no_putusan_pengadilan"
            'tanggal_putusan_pengadilan'  => $request->tgl_putusan_pengadilan,// name="tgl_putusan_pengadilan"
            'lama_pidana_denda'           => $request->lama_pidana, 
            'no_litmas'                   => $request->no_litmas,   

            // ── STEP 2 : KONDISI PENJAMIN ────────────────────────────────
            'perkawinan_penjamin'       => $request->penjamin_perkawinan,      // name="penjamin_perkawinan"
            'relasi_keluarga_penjamin'  => $request->penjamin_relasi_keluarga,// name="penjamin_relasi_keluarga"
            'relasi_masyarakat_penjamin'=> $request->penjamin_relasi_masyarakat,// name="penjamin_relasi_masyarakat"
            'pekerjaan_penjamin'        => $request->penjamin_pekerjaan,       // name="penjamin_pekerjaan"
            'kondisi_rumah_penjamin'    => $request->penjamin_rumah,           // name="penjamin_rumah"

            // ── STEP 3 : A. RIWAYAT KELAHIRAN ───────────────────────────
            'riwayat_kelahiran'   => $request->riwayat_kelahiran,   // name="riwayat_kelahiran"
            'riwayat_pertumbuhan' => $request->riwayat_pertumbuhan, // name="riwayat_pertumbuhan"
            'riwayat_perkembangan'=> $request->riwayat_perkembangan,// name="riwayat_perkembangan"

            // ── STEP 3 : B. PENDIDIKAN ──────────────────────────────────
            'pendidikan_keluarga' => $request->pendidikan_keluarga, // name="pendidikan_keluarga"
            'pendidikan_formal'   => $request->pendidikan_formal,   // name="pendidikan_formal"
            'pendidikan_nonformal'=> $request->pendidikan_nonformal,// name="pendidikan_nonformal"

            // ── STEP 3 : C. TINGKAH LAKU ────────────────────────────────
            'bakat_potensi'       => $request->bakat_potensi,    // name="bakat_potensi"
            'relasi_keluarga'     => $request->relasi_sosial,    // name="relasi_sosial"
            'ketaatan_agama'      => $request->ketaatan_agama,   // name="ketaatan_agama"
            'kebiasaan_baik'      => $request->kebiasaan_baik,   // name="kebiasaan_baik"
            'kebiasaan_buruk'     => $request->kebiasaan_buruk,  // name="kebiasaan_buruk"
            'sikap_bekerja'       => $request->sikap_kerja,      // name="sikap_kerja"
            'riwayat_pelanggaran' => $request->riwayat_hukum,   // name="riwayat_hukum"
            'riwayat_napza'       => $request->riwayat_zat,     // name="riwayat_zat"

            // ── STEP 3 : D. PERKAWINAN ──────────────────────────────────
            'riwayat_perkawinan'  => $request->riwayat_perkawinan, // name="riwayat_perkawinan"

            // ── STEP 3 : III. KONDISI LINGKUNGAN ────────────────────────
            'relasi_masyarakat_klien'       => $request->lingkungan_relasi,    // name="lingkungan_relasi"
            'kondisi_lingkungan_klien'      => $request->lingkungan_kondisi,   // name="lingkungan_kondisi"
            'profesi_masyarakat'            => $request->lingkungan_profesi,   // name="lingkungan_profesi"
            'ekonomi_masyarakat'             => $request->lingkungan_strata,    // name="lingkungan_strata"
            'tingkat_pendidikan_masyarakat' => $request->lingkungan_pendidikan,// name="lingkungan_pendidikan"
            'kehidupan_masyarakat'          => $request->kepedulian_masyarakat,// name="kepedulian_masyarakat"
            'kegiatan_pendidikan'           => $request->kepedulian_pendidikan,// name="kepedulian_pendidikan"
            'kegiatan_keagamaan'            => $request->kepedulian_agama,     // name="kepedulian_agama"
            'penegak_hukum'                 => $request->kepedulian_hukum,     // name="kepedulian_hukum"

            // ── STEP 3 : IV. RIWAYAT TINDAK PIDANA ─────────────────────
            'latar_belakang'   => $request->pidana_latar,       // name="pidana_latar"
            'kronologis'       => $request->pidana_kronologis,  // name="pidana_kronologis"
            'keadaan_korban'   => $request->pidana_korban,      // name="pidana_korban"
            'dampak_klien'     => $request->akibat_klien,       // name="akibat_klien"
            'dampak_keluarga'  => $request->akibat_keluarga,    // name="akibat_keluarga"
            'dampak_masyarakat'=> $request->akibat_masyarakat,  // name="akibat_masyarakat"

            // ── STEP 3 : V. TANGGAPAN ───────────────────────────────────
            'tanggapan_klien'     => $request->tanggapan_klien,     // name="tanggapan_klien"
            'tanggapan_keluarga'  => $request->tanggapan_keluarga,  // name="tanggapan_keluarga"
            'tanggapan_masyarakat'=> $request->tanggapan_masyarakat,// name="tanggapan_masyarakat"
            'tanggapan_pemerintah'=> $request->tanggapan_pemerintah,// name="tanggapan_pemerintah"

            // ── STEP 3 : VI. EVALUASI PEMBINAAN ─────────────────────────
            'program_admisi'     => $request->evaluasi_admisi,       // name="evaluasi_admisi"
            'sepertiga_pidana'         => $request->sepertiga_pidana,         // name="tgl_sepertiga"
            'seperdua_pidana'         => $request->seperdua_pidana,          // name="tgl_setengah"
            'duapertiga_pidana'         => $request->duapertiga_pidana,        // name="tgl_duapertiga"
            'program_kepribadian'=> $request->pembinaan_kepribadian, // name="pembinaan_kepribadian"
            'program_kemandirian'=> $request->pembinaan_kemandirian, // name="pembinaan_kemandirian"
            'warga_binaan'       => $request->relasi_wbp,            // name="relasi_wbp"
            'petugas'            => $request->relasi_petugas,        // name="relasi_petugas"
            'keluarga'           => $request->relasi_keluarga,       // name="relasi_keluarga"
            'masyarakat'         => $request->relasi_masyarakat,     // name="relasi_masyarakat"

            // ── STEP 3 : VII. HASIL ASESMEN ─────────────────────────────
            'rekomendasi_asesmen'  => $request->hasil_asesmen,       // name="hasil_asesmen"

            // ── STEP 3 : VIII. ANALISIS ─────────────────────────────────
            'sikap_klien_pembinaan' => $request->analisis_resiko,    // name="analisis_resiko"
            'hasil_setelah_program' => $request->analisis_hasil,     // name="analisis_hasil"
            'kesiapan_masyarakat'   => $request->analisis_penerimaan,// name="analisis_penerimaan"

            // ── STEP 3 : IX. KESIMPULAN ─────────────────────────────────
            'tgl_rekomendasi' => $request->tgl_rekomendasi,
            'kesimpulan'  => $request->kesimpulan,   // name="kesimpulan"
            'rekomendasi' => $request->rekomendasi,  // name="rekomendasi"
    ]);

    // simpan klasifikasi hukum
    if ($request->pasal_ids) {
        // Ambil klasifikasi_hukum_id dari tiap pasal yang dipilih
        $klasifikasiIds = \App\Models\Pasal::whereIn('id', $request->pasal_ids)
            ->pluck('klasifikasi_hukum_id')
            ->filter()       // buang null
            ->unique()
            ->values()
            ->toArray();

        $litmas->klasifikasiHukum()->sync($klasifikasiIds);
    }

    // simpan keluarga
    if ($request->nama) {
        foreach ($request->nama as $i => $nama) {
            if (!$nama) continue;

            family::create([
                'p_b_dewasa_id' => $litmas->id,
                'client_id'     => $litmas->client_id, // ← TAMBAH INI
                'nama'          => $nama,
                'jk'            => $request->jk[$i] ?? null,
                'usia'          => $request->usia[$i] ?? null,
                'pendidikan'    => $request->pendidikan[$i] ?? null,
                'pekerjaan'     => $request->pekerjaan[$i] ?? null,
                'keterangan'    => $request->keterangan[$i] ?? null,
                'no_kk'         => $request->no_kk ?? null,
            ]);
        }
    }

    return redirect()->route('export.preview', $litmas->id)
        ->with('success', 'Data berhasil disimpan');
}
    /**
     * ============================
     * GET DATA + RELASI
     * ============================
     */
    private function getLitmasData($id)
    {
        return PBDewasa::with([
            'client',
            'user',
            // 'guarantors',
            'guarantor',
            'klasifikasiHukum',
            'families'
        ])->findOrFail($id);
    }

    /**
     * ============================
     * FORMAT PERKARA
     * ============================
     */
    private function formatPerkara($litmas)
    {
        $dasar = $litmas->klasifikasiHukum
            ->pluck('nama_klasifikasi')
            ->toArray();

        if (count($dasar) > 1) {
            $last = array_pop($dasar);
            $dasarHukum = implode(', ', $dasar) . ' dan ' . $last;
        } else {
            $dasarHukum = $dasar[0] ?? '-';
        }

        return $litmas->perkara . ' / ' . $dasarHukum;
    }

    /**
     * ================================
     * MERUBAH BENTUK TANGGAL YYYY/MM/DD KE 2 JANUARI 2026
     * ================================
     */

    private function formatTanggalIndo($tanggal)
    {
        if (!$tanggal || $tanggal == '-') return '-';

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $date = date_create($tanggal);
        if (!$date) return '-';

        $hari = date_format($date, 'd');
        $bulanIndex = (int) date_format($date, 'm');
        $tahun = date_format($date, 'Y');

        return ltrim($hari, '0') . ' ' . $bulan[$bulanIndex] . ' ' . $tahun;
    }

    /**
     * ==========================
     * YYYY/MM/DD KE HARI, TANGGAL
     * 
     */

    private function formatTanggalHariIndo($tanggal)
    {
        if (!$tanggal || $tanggal == '-') return '-';

        $hariIndo = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $date = date_create($tanggal);
        if (!$date) return '-';

        $hari = $hariIndo[date_format($date, 'l')];
        $tanggalIndo = $this->formatTanggalIndo($tanggal);

        return $hari . ', ' . $tanggalIndo;
    }
    

    /**
     * ============================
     * BUILD DATA (SEMUA PLACEHOLDER)
     * ============================
     */
    private function buildTemplateData($litmas, $perkara)
    {
        // $guarantors = $litmas->guarantors ?? collect();

        $guarantors = \App\Models\Guarantor::where('client_id', $litmas->client_id)->get();

        // AYAH
        $ayah = $guarantors->first(function ($g) {
            return strtolower($g->hubungan_keluarga) === 'ayah kandung';
        });

        // IBU
        $ibu = $guarantors->first(function ($g) {
            return strtolower($g->hubungan_keluarga) === 'ibu kandung';
        });

        return [

            // ================= NOTA DINAS =============
            'no_nota_dinas' => $litmas->no_nota_dinas ?? '-',   // name="no_nota_dinas"
            'tgl_nota_dinas' => $this->formatTanggalIndo($litmas->tanggal_nota_dinas) ?? '-',  // name="tgl_nota_dinas"
            'asal_surat_rujukan' => $litmas->asal_surat_rujukan ?? '-',          // name="kepada"
            'no_surat_rujukan'   => $litmas->no_surat_rujukan ?? '-',        // name="no_surat"
            'tgl_rujukan'  => $this->formatTanggalIndo($litmas->tgl_surat_rujukan) ?? '-',
            'no_reg_rutan' => $litmas->no_reg_rutan ?? '-',
            'tgl_wawancara' => $this->formatTanggalIndo($litmas->tanggal_studi_literatur) ?? '-',
            'sumber_informasi' => $litmas->saksi ?? '-',
            

            // ================= CLIENT =================
            'nama_klien' => $litmas->client->nama ?? '-',
            'nama_klien_upper' => strtoupper($litmas->client->nama ?? '-'),
            'ttl_klien' => $this->formatTanggalIndo($litmas->client->tanggal_lahir) ?? '-',
            'tmpt_lahir' => $litmas->client->tempat_lahir ?? '-',
            'alamat_klien' => $litmas->client->alamat ?? '-',
            'agama_klien' => $litmas->client->agama ?? '-',
            'jk_klien' => $litmas->client->jenis_kelamin ?? '-',
            'perkawinan_klien' => $litmas->client->status_perkawinan ?? '-',
            'pendidikan_klien' => $litmas->client->pendidikan ?? '-',
            'pekerjaan_klien' => $litmas->client->pekerjaan ?? '-',
            'suku' => $litmas->client->suku ?? '-',
            'bangsa' => $litmas->client->kebangsaan ?? '-',
            'kewarganegaraan' => $litmas->client->kewarganegaraan ?? '-',
            'ciri_khusus_klien' => $litmas->client->ciri_khusus ?? '-',

            // ================= USER =================
            'nama_user' => $litmas->user->name ?? '-',
            'nama_user_upper' => strtoupper($litmas->user->name ?? '-'),
            'nip' => $litmas->nip ?? '-',
            'jabatan' => $litmas->jabatan ?? '-',
            'jabatan_upper' => strtoupper($litmas->jabatan ?? '-'),

            // ================= PENJAMIN =================
            'nama_penjamin' => $litmas->guarantor->nama ?? '-',
            'alamat_penjamin' => $litmas->guarantor->alamat ?? '-',

            // ================= PERKARA =================
            'perkara' => $perkara,
            'perkara_upper' => strtoupper($perkara),

            // ================= AYAH =================
            'nama_ayah'      => $ayah->nama ?? '-',
            'ttl_ayah' => ($ayah->tempat_lahir ?? '-') . ', ' . $this->formatTanggalIndo($ayah->tanggal_lahir ?? ''),
            'agama_ayah'     => $ayah->agama ?? '-',
            'suku_ayah'      => $ayah->suku ?? '-',
            'bangsa_ayah'    => ($ayah->suku ?? '-') . ' / ' . ($ayah->kewarganegaraan ?? '-'),  // ← TAMBAH
            'wn_ayah'        => $ayah->kewarganegaraan ?? '-',
            'pendidikan_ayah'=> $ayah->pendidikan_terakhir ?? '-',
            'pekerjaan_ayah' => $ayah->pekerjaan ?? '-',   // ← sekalian fix bug suku sebelumnya
            'alamat_ayah'   => $ayah->alamat ?? '-',
            'hub_kel_ayah'   => $ayah->hubungan_keluarga ?? '-',

            // ================= IBU =================
            'nama_ibu'       => $ibu->nama ?? '-',
            'ttl_ibu' => ($ibu->tempat_lahir ?? '-') . ', ' . $this->formatTanggalIndo($ibu->tanggal_lahir ?? ''),
            'agama_ibu'      => $ibu->agama ?? '-',
            'suku_ibu'       => $ibu->suku ?? '-',
            'bangsa_ibu'     => ($ibu->suku ?? '-') . ' / ' . ($ibu->kewarganegaraan ?? '-'),    // ← TAMBAH
            'wn_ibu'         => $ibu->kewarganegaraan ?? '-',
            'pendidikan_ibu' => $ibu->pendidikan_terakhir ?? '-',
            'pekerjaan_ibu'  => $ibu->pekerjaan ?? '-',    // ← sekalian fix bug suku sebelumnya
            'alamat_ibu'   => $ibu->alamat ?? '-',
            'hub_kel_ibu'    => $ibu->hubungan_keluarga ?? '-',

            // ============== KELUARGA ===============
            'no_kk' => $litmas->families->first()->no_kk ?? '-',

            // ================= DATA UMUM =================
            'no_litmas' => $litmas->no_litmas ?? '-',
            'no_putusan' => $litmas->no_putusan_pengadilan ?? '-',
            'tgl_putusan' => $this->formatTanggalIndo($litmas->tanggal_putusan_pengadilan) ?? '-',
            'lama_pidana' => $litmas->lama_pidana_denda ?? '-',

            // ================= RIWAYAT HIDUP DAN PERKEMBANGAN KLIEN =================
            // ================= RIWAYAT KELAHIRAN KLIEN ==================
            'kelahiran_klien' => $litmas->riwayat_kelahiran ?? '-',
            'pertumbuhan_klien' => $litmas->riwayat_pertumbuhan ?? '-',
            'perkembangan_klien' => $litmas->riwayat_perkembangan?? '-',

            // ================== RIWAYAT PENDIDIKAN KLIEN =================
            'pendd_keluarga' => $litmas->pendidikan_keluarga ?? '-',
            'pendd_formal' => $litmas->pendidikan_formal ?? '-',
            'pendd_nonformal' => $litmas->pendidikan_nonformal ?? '-',

            // ================== RIWAYAT TINGKAH LAKU KLIEN ==================
            'bakat_klien' => $litmas->bakat_potensi ?? '-',
            'relasi_keluarga' => $litmas->relasi_keluarga ?? '-',
            'ketaatan_klien' => $litmas->ketaatan_agama ?? '-',
            'kebiasaan_baik_klien' => $litmas->kebiasaan_baik ?? '-',
            'kebiasaan_jelek_klien' => $litmas->kebiasaan_buruk ?? '-',
            'sikap_bekerja' => $litmas->sikap_bekerja ?? '-',
            'pelanggaran_klien' => $litmas->riwayat_pelanggaran ?? '-',
            'rokok_napza_alkohol' => $litmas->riwayat_napza ?? '-',

            // =============== RIWAYAT PERKAWINAN KLIEN ==========================
            'rwyt_kawin_klien' => $litmas->riwayat_perkawinan ?? '-',

            // =============== KONDISI SOSIAL LINGKUNGAN TEMPAT TINGGAL PENJAMIN ===============
            'kawin_penjamin' => $litmas->perkawinan_penjamin ?? '-',
            'rs_kel' => $litmas->relasi_keluarga_penjamin ?? '-',
            'rs_mas' => $litmas->relasi_masyarakat_penjamin ?? '-',

            // ====================== PEKERJAAN DAN KEADAAN EKONOMI ==============
            'pekerjaan_penjamin' => $litmas->pekerjaan_penjamin ?? '-',
            'rt_penjamin' => $litmas->kondisi_rumah_penjamin ?? '-',

            // ===================== KONDISI LINGKUNGAN SOSIAL, BUDAYA TEMPAT TINGGAL KLIEN =================
            'rs_mas_klien' => $litmas->relasi_masyarakat_klien ?? '-',
            'eko_budaya_pen_klien' => $litmas->kondisi_lingkungan_klien ?? '-',

            // ===================== KEADAAN MASYARAKAT ==========================
            'profesi_mas' => $litmas->profesi_masyarakat ?? '-',
            'ekonomi_mas' => $litmas->ekonomi_masyarakat ?? '-',
            'tingkat_pendd_mas' => $litmas->tingkat_pendidikan_masyarakat ?? '-',

            // ======================= INTERAKSI SOSIAL DALAM MASYARAKAT ==============
            'ph_mas' => $litmas->kehidupan_masyarakat ?? '-',
            'ph_pend' => $litmas->kegiatan_pendidikan ?? '-',
            'ph_agama' => $litmas->kegiatan_keagamaan ?? '-',
            'ph_hukum_norma' => $litmas->penegak_hukum ?? '-',

            // ====================== RIWAYAT TINDAK PIDANA ================
            'latar_blkg' => $litmas->latar_belakang ?? '-',
            'kronologis' => $litmas->kronologis ?? '-',
            'keadaan_korban' => $litmas->keadaan_korban ?? '-',

            // ===================== AKIBAT YANG DITIMBULKAN ================
            'akibat_klien' => $litmas->dampak_klien ?? '-',
            'akibat_kel' => $litmas->dampak_keluarga ?? '-',
            'akibat_mas' => $litmas->dampak_masyarakat ?? '-',
            
            // ================== TANGGAPAN KLIEN, KELUARGA, KORBAN, MASYARAKAT =============
            'tanggapan_klien' => $litmas->tanggapan_klien ?? '-',
            'tanggapan_kel' => $litmas->tanggapan_keluarga ?? '-',
            'tanggapan_mas' => $litmas->tanggapan_masyarakat ?? '-',
            'tanggapan_pemerintah' => $litmas->tanggapan_pemerintah ?? '-',

            // ======================== EVALUASI PERKEMBANGAN PEMBINAAN ================
            // ======================== PROGRAM ADMISI, ORIENTASI, DAN OBSERVASI ========
            'program_admisi' => $litmas->program_admisi ?? '-',
            'sepertiga_pidana' => $this->formatTanggalIndo($litmas->sepertiga_pidana) ?? '-',
            'seperdua_pidana' => $this->formatTanggalIndo($litmas->seperdua_pidana) ?? '-',
            'duapertiga_pidana' => $this->formatTanggalIndo($litmas->duapertiga_pidana) ?? '-',
            
            // ==================== PROGRAM PEMBINAAN KEMANDIRIAN DAN KEPRIBADIAN
            'prog_kepribadian' => $litmas->program_kepribadian ?? '-',
            'prog_kemandirian' => $litmas->program_kemandirian ?? '-',

            // ==================== RELASI SOSIAL SELAMA DI DALAM RUTAN =============
            'rutan_wbm' => $litmas->warga_binaan ?? '-',
            'rutan_petugas' => $litmas->petugas ?? '-',
            'rutan_kel' => $litmas->keluarga ?? '-',
            'rutan_mas' => $litmas->masyarakat ?? '-',

            // ==================== HASIL ASESMEN =====================
            'hasil' => $litmas->rekomendasi_asesmen ?? '-',
            
            //========================= ANALISIS ======================
            'sikap_klien' => $litmas->sikap_klien_pembinaan ?? '-',
            'hasil_prog_binaan' => $litmas->hasil_setelah_program ?? '-',
            'penerimaan_mas' => $litmas->kesiapan_masyarakat ?? '-',
            
            'kesimpulan' => $litmas->kesimpulan ?? '-',
            'tgl_rekomendasi' => $this->formatTanggalHariIndo($litmas->tgl_rekomendasi) ?? '-',
            'rekomendasi' => $litmas->rekomendasi ?? '-',
        ];
    }

    /**
     * ============================
     * EXPORT WORD
     * ============================
     */
    public function exportWord($id)
    {
        $litmas = $this->getLitmasData($id);
        $perkara = $this->formatPerkara($litmas);

        $template = new TemplateProcessor(
            storage_path('app/public/templates/litmas/litmas.docx')
        );

        // AUTO SET ALL DATA
        $data = $this->buildTemplateData($litmas, $perkara);

        foreach ($data as $key => $value) {
            $template->setValue($key, $value);
        }

        // =========================
        // TABLE KELUARGA
        // =========================
        if ($litmas->families->count()) {
            $template->cloneRow('nama', $litmas->families->count());

            foreach ($litmas->families as $i => $f) {
                $index = $i + 1;
                $template->setValue("nama#$index",       $f->nama ?? '-');
                $template->setValue("jk#$index",         $f->jk == 'L' ? 'Laki-laki' : 'Perempuan');
                $template->setValue("usia#$index",       ($f->usia ?? '-') . ' tahun');
                $template->setValue("pendidikan#$index", $f->pendidikan ?? '-');
                $template->setValue("pekerjaan#$index",  $f->pekerjaan ?? '-');
                $template->setValue("ket#$index",        $f->keterangan ?? '-');
            }
        } else {
            // ← GANTI: clear SEMUA placeholder di baris tabel sekaligus
            $template->setValue('nama',       '-');
            $template->setValue('jk',         '-');
            $template->setValue('usia',       '-');
            $template->setValue('pendidikan', '-');
            $template->setValue('pekerjaan',  '-');
            $template->setValue('ket',        '-');
        }

        // SAVE FILE
        $fileName = 'litmas_' . time() . '.docx';
        $path = storage_path($fileName);

        $template->saveAs($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function preview($id)
{
    $litmas = $this->getLitmasData($id);
    $perkara = $this->formatPerkara($litmas);

    return view('litmas.preview', compact('litmas', 'perkara'));
}
}