<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\family;
use App\Models\PBDewasa;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * ============================
     * STORE DATA
     * ============================
     */
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
            'user_id' => $client->user_id, // 🔥 INI YANG BENAR
            'guarantor_id' => $request->guarantor_id,
            'perkara' => $request->perkara,
        ]);

        // Relasi dasar hukum
        $litmas->klasifikasiHukum()->sync($request->klasifikasi_hukum_ids ?? []);

        // Simpan keluarga
        $this->storeFamilies($litmas->id, $request);

        return redirect()->route('export.preview', $litmas->id)
            ->with('success', 'Data berhasil disimpan');
    }

    /**
     * ============================
     * SIMPAN KELUARGA
     * ============================
     */
    private function storeFamilies($litmasId, $request)
    {
        if (!$request->nama) return;

        foreach ($request->nama as $i => $nama) {
            if (!$nama) continue;

            family::create([
                'p_b_dewasa_id' => $litmasId,
                'nama' => $nama,
                'jk' => $request->jk[$i] ?? null,
                'usia' => $request->usia[$i] ?? null,
                'pendidikan' => $request->pendidikan[$i] ?? null,
                'pekerjaan' => $request->pekerjaan[$i] ?? null,
                'keterangan' => $request->keterangan[$i] ?? null,
            ]);
        }
    }

    /**
     * ============================
     * GET DATA
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
     * PREVIEW
     * ============================
     */
    public function preview($id)
    {
        $litmas = $this->getLitmasData($id);
        $perkara = $this->formatPerkara($litmas);

        return view('litmas.preview', compact('litmas', 'perkara'));
    }

//     public function preview($id)
// {
//     $litmas = $this->getLitmasData($id);

//     dd($litmas); // <- cek ini
// }

    /**
     * ============================
     * EXPORT WORD
     * ============================
     */
    public function exportWord($id)
    {
        $litmas = $this->getLitmasData($id);
        $perkara = $this->formatPerkara($litmas);

        $template = $this->getTemplate();

        $this->setBasicValues($template, $litmas, $perkara);
        $this->setFamilies($template, $litmas->families);

        $fileName = 'litmas_' . time() . '.docx';
        $path = storage_path($fileName);

        $template->saveAs($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    /**
     * ============================
     * EXPORT PDF
     * ============================
     */
    public function exportPDF($id)
    {
        $litmas = $this->getLitmasData($id);
        $perkara = $this->formatPerkara($litmas);

        $pdf = Pdf::loadView('litmas.preview', compact('litmas', 'perkara'));

        return $pdf->download('litmas.pdf');
    }

    /**
     * ============================
     * LOAD TEMPLATE
     * ============================
     */
    private function getTemplate()
    {
        return new TemplateProcessor(
            storage_path('app/templates/litmas.docx')
        );
    }

    /**
     * ============================
     * SET BASIC DATA
     * ============================
     */
    private function setBasicValues($template, $litmas, $perkara)
    {
        // CLIENT
        $template->setValue('nama_klien', $litmas->client->nama ?? '-');
        strtoupper($litmas->client->nama ?? '-');

        // USER
        $template->setValue('nama_user', $litmas->user->name ?? '-');
        strtoupper($litmas->user->name ?? '-');
        // PERKARA
        $template->setValue('perkara', $perkara);
        $template->setValue('perkara_upper', strtoupper($perkara));

        // PENJAMIN
        $template->setValue('nama_penjamin', $litmas->guarantor->nama ?? '-');
    }

    /**
     * ============================
     * SET KELUARGA
     * ============================
     */
    private function setFamilies($template, $families)
    {
        if ($families->isEmpty()) {
            $template->setValue('nama', '-');
            return;
        }

        $template->cloneRow('nama', count($families));

        foreach ($families as $i => $f) {
            $index = $i + 1;

            $template->setValue("nama#$index", $f->nama);
            $template->setValue("jk#$index", $this->formatJK($f->jk));
            $template->setValue("usia#$index", ($f->usia ?? '-') . ' tahun');
            $template->setValue("pendidikan#$index", $f->pendidikan ?? '-');
            $template->setValue("pekerjaan#$index", $f->pekerjaan ?? '-');
            $template->setValue("ket#$index", $f->keterangan ?? '-');
        }
    }

    /**
     * ============================
     * FORMAT JK
     * ============================
     */
    private function formatJK($jk)
    {
        return $jk == 'L' ? 'Laki-laki' : 'Perempuan';
    }
}