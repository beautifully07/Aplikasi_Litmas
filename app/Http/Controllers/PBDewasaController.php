<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PBDewasa;
use App\Models\Client;
use App\Models\KlasifikasiHukum;
use App\Models\Family;

class PBDewasaController extends Controller
{
    public function create()
    {
        $clients = Client::all();
        $klasifikasi = KlasifikasiHukum::all();

        return view('litmas.create', compact('clients', 'klasifikasi'));
    }

    // =========================
    // AUTO FILL CLIENT
    // =========================
    public function getClient($id)
    {
        $client = Client::with(['user', 'guarantor'])->findOrFail($id);

        return response()->json([
            'user_id' => $client->user_id,
            'guarantor_id' => $client->guarantor_id,
            'user_name' => $client->user->name ?? null,
            'guarantor_name' => $client->guarantor->name ?? null,
        ]);
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'klasifikasi_hukums_id' => 'required',
        ]);

        $litmas = PBDewasa::create([
            // NOTA DINAS
            'no_nota_dinas' => $request->no_nota_dinas,
            'tanggal_nota_dinas' => $request->tanggal_nota_dinas,
            'asal_surat_rujukan' => $request->asal_surat_rujukan,
            'no_surat_rujukan' => $request->no_surat_rujukan,
            'tgl_surat_rujukan' => $request->tgl_surat_rujukan,
            'no_reg_rutan' => $request->no_reg_rutan,

            // COVER
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,

            // DATA UTAMA
            'no_litmas' => $request->no_litmas,
            'tanggal_litmas' => $request->tanggal_litmas,
            'perkara' => $request->perkara,
            'no_putusan_pengadilan' => $request->no_putusan_pengadilan,
            'tanggal_putusan_pengadilan' => $request->tanggal_putusan_pengadilan,
            'lama_pidana_denda' => $request->lama_pidana_denda,

            // RELASI
            'client_id' => $request->client_id,
            'user_id' => $request->user_id,
            'guarantor_id' => $request->guarantor_id,
            'klasifikasi_hukums_id' => $request->klasifikasi_hukums_id,
        ]);

        // =========================
        // FAMILY
        // =========================
        if ($request->family) {
            foreach ($request->family as $item) {
                Family::create([
                    'client_id' => $request->client_id,
                    'nama' => $item['nama'],
                    'hubungan' => $item['hubungan'],
                    'umur' => $item['umur'],
                    'pekerjaan' => $item['pekerjaan'],
                ]);
            }
        }

        return redirect()->route('litmas.index')->with('success', 'Data berhasil disimpan');
    }

    // =========================
    // EDIT
    // =========================
    public function edit($id)
    {
        $litmas = PBDewasa::findOrFail($id);
        $clients = Client::all();
        $klasifikasi = KlasifikasiHukum::all();
        $families = Family::where('client_id', $litmas->client_id)->get();

        return view('litmas.edit', compact('litmas', 'clients', 'klasifikasi', 'families'));
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, $id)
    {
        $litmas = PBDewasa::findOrFail($id);

        $litmas->update($request->all());

        // reset family
        Family::where('client_id', $request->client_id)->delete();

        if ($request->family) {
            foreach ($request->family as $item) {
                Family::create([
                    'client_id' => $request->client_id,
                    'nama' => $item['nama'],
                    'hubungan' => $item['hubungan'],
                    'umur' => $item['umur'],
                    'pekerjaan' => $item['pekerjaan'],
                ]);
            }
        }

        return redirect()->route('litmas.index')->with('success', 'Data berhasil diupdate');
    }

    // =========================
    // DELETE
    // =========================
    public function destroy($id)
    {
        PBDewasa::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}