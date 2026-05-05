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
        'client_id' => $request->client_id,
        'user_id' => $client->user_id,
        'guarantor_id' => $request->guarantor_id,
        'perkara' => $request->perkara,
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
            'ttl_klien' => $litmas->client->ttl ?? '-',
            'alamat_klien' => $litmas->client->alamat ?? '-',
            'agama_klien' => $litmas->client->agama ?? '-',
            'jk_klien' => $litmas->client->jk ?? '-',
            'perkawinan_klien' => $litmas->client->perkawinan ?? '-',
            'pendidikan_klien' => $litmas->client->pendidikan ?? '-',
            'pekerjaan_klien' => $litmas->client->pekerjaan ?? '-',
            'suku' => $litmas->client->suku ?? '-',
            'bangsa' => $litmas->client->bangsa ?? '-',
            'kewarganegaraan' => $litmas->client->kewarganegaraan ?? '-',
            'ciri_khusus_klien' => $litmas->client->ciri_khusus ?? '-',

            // ================= USER =================
            'nama_user' => $litmas->user->name ?? '-',
            'nama_user_upper' => strtoupper($litmas->user->name ?? '-'),
            'nip' => $litmas->user->nip ?? '-',
            'jabatan' => $litmas->user->jabatan ?? '-',
            'jabatan_upper' => strtoupper($litmas->user->jabatan ?? '-'),

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
            'no_litmas' => $litmas->id ?? '-',
            'no_putusan' => $litmas->no_putusan ?? '-',
            'tgl_putusan' => $litmas->tgl_putusan ?? '-',
            'lama_pidana' => $litmas->lama_pidana ?? '-',

            // ================= NARASI =================
            'kelahiran_klien' => $litmas->kelahiran_klien ?? '-',
            'pertumbuhan_klien' => $litmas->pertumbuhan_klien ?? '-',
            'perkembangan_klien' => $litmas->perkembangan_klien ?? '-',

            'pendd_keluarga' => $litmas->pendd_keluarga ?? '-',
            'pendd_formal' => $litmas->pendd_formal ?? '-',
            'pendd_nonformal' => $litmas->pendd_nonformal ?? '-',

            'bakat_klien' => $litmas->bakat_klien ?? '-',
            'relasi_keluarga' => $litmas->relasi_keluarga ?? '-',
            'ketaatan_klien' => $litmas->ketaatan_klien ?? '-',

            'kebiasaan_baik_klien' => $litmas->kebiasaan_baik_klien ?? '-',
            'kebiasaan_jelek_klien' => $litmas->kebiasaan_jelek_klien ?? '-',

            'sikap_klien' => $litmas->sikap_klien ?? '-',
            'pelanggaran_klien' => $litmas->pelanggaran_klien ?? '-',
            'rokok_napza_alkohol' => $litmas->rokok_napza_alkohol ?? '-',

            'rwyt_kawin_klien' => $litmas->rwyt_kawin_klien ?? '-',

            'latar_blkg' => $litmas->latar_blkg ?? '-',
            'kronologis' => $litmas->kronologis ?? '-',
            'keadaan_korban' => $litmas->keadaan_korban ?? '-',

            'tanggapan_klien' => $litmas->tanggapan_klien ?? '-',
            'tanggapan_kel' => $litmas->tanggapan_kel ?? '-',
            'tanggapan_mas' => $litmas->tanggapan_mas ?? '-',
            'tanggapan_pemerintah' => $litmas->tanggapan_pemerintah ?? '-',

            'hasil' => $litmas->hasil ?? '-',
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
            storage_path('app/templates/litmas/litmas.docx')
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