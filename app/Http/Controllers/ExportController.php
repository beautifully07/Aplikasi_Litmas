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
        'asal_surat_rujukan' => $request->kepada,          // name="kepada"
        'no_surat_rujukan'   => $request->no_surat,        // name="no_surat"
        'tgl_surat_rujukan'  => $request->tanggal_surat,   // name="tanggal_surat" 'no_reg_rutan'=> $request->no_register, // name="no_register"
        'perkara' => $request->perkara,

        // ── STEP 2 : PK & IDENTITAS ─────────────────────────────────
            'nip'                         => $request->nip,                    // name="nip"
            'jabatan'                     => $request->jabatan,                // name="jabatan"
            'tanggal_studi_literatur'     => $request->tanggal_wawancara,     // name="tanggal_wawancara"
            'saksi'                       => $request->sumber_informasi,       // name="sumber_informasi"
            'no_putusan_pengadilan'       => $request->no_putusan_pengadilan, // name="no_putusan_pengadilan"
            'tanggal_putusan_pengadilan'  => $request->tgl_putusan_pengadilan,// name="tgl_putusan_pengadilan"
            'lama_pidana_denda'           => $request->lama_pidana,           // name="lama_pidana"

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
            'eknomi_masyarakat'             => $request->lingkungan_strata,    // name="lingkungan_strata"
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
            '1/3_pidana'         => $request->tgl_sepertiga,         // name="tgl_sepertiga"
            '1/2_pidana'         => $request->tgl_setengah,          // name="tgl_setengah"
            '2/3_pidana'         => $request->tgl_duapertiga,        // name="tgl_duapertiga"
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
            'kesimpulan'  => $request->kesimpulan,   // name="kesimpulan"
            'rekomendasi' => $request->rekomendasi,  // name="rekomendasi"
    ]);

    // simpan klasifikasi hukum
    $litmas->klasifikasiHukum()->sync($request->klasifikasi_hukum_ids ?? []);

    // simpan keluarga
    if ($request->nama) {
        foreach ($request->nama as $i => $nama) {
            if (!$nama) continue;

            family::create([
                'p_b_dewasa_id' => $litmas->id,
                'nama' => $nama,
                'jk' => $request->jk[$i] ?? null,
                'usia' => $request->usia[$i] ?? null,
                'pendidikan' => $request->pendidikan[$i] ?? null,
                'pekerjaan' => $request->pekerjaan[$i] ?? null,
                'keterangan' => $request->keterangan[$i] ?? null,
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
     * ============================
     * BUILD DATA (SEMUA PLACEHOLDER)
     * ============================
     */
    private function buildTemplateData($litmas, $perkara)
    {
        return [

            // ================= CLIENT =================
            'nama_klien' => $litmas->client->nama ?? '-',
            'nama_klien_upper' => strtoupper($litmas->client->nama ?? '-'),
            'ttl_klien' => $litmas->client->tanggal_lahir ?? '-',
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
            'nama_ayah' => $litmas->guarantor->nama ?? '-',
            'ttl_ayah' => $litmas->guarantor->ttl ?? '-',
            'agama_ayah' => $litmas->guarantor->agama ?? '-',
            'alamat_ayah' => $litmas->guarantor->alamat ?? '-',

            // ================= IBU =================
            'nama_ibu' => '-',
            'ttl_ibu' => '-',
            'agama_ibu' => '-',
            'alamat_ibu' => '-',

            // ================= DATA UMUM =================
            'no_litmas' => $litmas->no_litmas ?? '-',
            'no_putusan' => $litmas->no_putusan_pengadilan ?? '-',
            'tgl_putusan' => $litmas->tanggal_putusan_pengadilan ?? '-',
            'lama_pidana' => $litmas->lama_pidana_denda ?? '-',

            // ================= NARASI =================
            'kelahiran_klien' => $litmas->riwayat_kelahiran ?? '-',
            'pertumbuhan_klien' => $litmas->riwayat_pertumbuhan ?? '-',
            'perkembangan_klien' => $litmas->riwayat_perkembangan?? '-',

            'pendd_keluarga' => $litmas->pendidikan_keluarga ?? '-',
            'pendd_formal' => $litmas->pendidikan_formal ?? '-',
            'pendd_nonformal' => $litmas->pendidikan_nonformal ?? '-',

            'bakat_klien' => $litmas->bakat_potensi ?? '-',
            'relasi_keluarga' => $litmas->relasi_keluarga ?? '-',
            'ketaatan_klien' => $litmas->ketaatan_agama ?? '-',

            'kebiasaan_baik_klien' => $litmas->kebiasaan_baik ?? '-',
            'kebiasaan_jelek_klien' => $litmas->kebiasaan_buruk ?? '-',

            'sikap_klien' => $litmas->sikap_bekerja ?? '-',
            'pelanggaran_klien' => $litmas->riwayat_pelanggaran ?? '-',
            'rokok_napza_alkohol' => $litmas->riwayat_napza ?? '-',

            'rwyt_kawin_klien' => $litmas->riwayat_perkawinan ?? '-',

            'latar_blkg' => $litmas->latar_belakang ?? '-',
            'kronologis' => $litmas->kronologis ?? '-',
            'keadaan_korban' => $litmas->keadaan_korban ?? '-',

            'tanggapan_klien' => $litmas->tanggapan_klien ?? '-',
            'tanggapan_kel' => $litmas->tanggapan_keluarga ?? '-',
            'tanggapan_mas' => $litmas->tanggapan_masyarakat ?? '-',
            'tanggapan_pemerintah' => $litmas->tanggapan_pemerintah ?? '-',

            'hasil' => $litmas->hasil_setelah_program ?? '-',
            
            'kesimpulan' => $litmas->kesimpulan ?? '-',
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

                $template->setValue("nama#$index", $f->nama ?? '-');
                $template->setValue("jk#$index", $f->jk == 'L' ? 'Laki-laki' : 'Perempuan');
                $template->setValue("usia#$index", ($f->usia ?? '-') . ' tahun');
                $template->setValue("pendidikan#$index", $f->pendidikan ?? '-');
                $template->setValue("pekerjaan#$index", $f->pekerjaan ?? '-');
                $template->setValue("ket#$index", $f->keterangan ?? '-');
            }
        } else {
            $template->setValue('nama', '-');
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