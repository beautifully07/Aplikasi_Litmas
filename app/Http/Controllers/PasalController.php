<?php

namespace App\Http\Controllers;

use App\Models\Pasal;
use App\Models\Ayat;
use App\Models\KlasifikasiHukum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PasalController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Pasal::with(['ayats','klasifikasiHukum']);

        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request){
                $q->where('nomor_pasal', 'like', '%' . $request->search . '%')
                  ->orWhere('judul', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->get('per_page', 10);

        $pasals = $query->paginate($perPage)->withQueryString();

        return view('pasal.index', compact('pasals','perPage'));
    }


    public function create()
    {
        $klasifikasi = KlasifikasiHukum::all();

        return view('pasal.create', compact('klasifikasi'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_klasifikasi' => 'required',
            'nomor_pasal' => 'required',
            'ayat.*.nomor_ayat' => 'required',
            'ayat.*.isi' => 'required'
        ]);

        /*
        ==========================
        CEK ATAU BUAT KLASIFIKASI
        ==========================
        */

        $klasifikasi = KlasifikasiHukum::firstOrCreate([
            'nama_klasifikasi' => $request->nama_klasifikasi
        ]);

        /*
        ==========================
        SIMPAN PASAL
        ==========================
        */

        $pasal = Pasal::create([
            'klasifikasi_hukum_id' => $klasifikasi->id,
            'nomor_pasal' => $request->nomor_pasal
        ]);

        /*
        ==========================
        SIMPAN AYAT
        ==========================
        */

       if ($request->has('ayat')) {

         foreach ($request->ayat as $ayat) {

            Ayat::create([
                'pasal_id' => $pasal->id,
                'nomor_ayat' => $ayat['nomor_ayat'],
                'isi' => $ayat['isi']
            ]);

        }

}

        return redirect()->route('pasal.index')
            ->with('success', 'Data berhasil disimpan');
    }


    public function show($id)
    {
        $pasal = Pasal::with(['ayats','klasifikasiHukum'])->findOrFail($id);

        return view('pasal.show', compact('pasal'));
    }


    public function edit($id)
    {
        $pasal = Pasal::with('ayats')->findOrFail($id);
        $klasifikasi = KlasifikasiHukum::all();

        return view('pasal.edit', compact('pasal','klasifikasi'));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'klasifikasi_hukum_id' => 'required',
            'nomor_pasal' => 'required',
            'ayat.*.nomor_ayat' => 'required',
            'ayat.*.isi' => 'required',
        ]);

        $pasal = Pasal::findOrFail($id);

        $pasal->update([
            'klasifikasi_hukum_id' => $request->klasifikasi_hukum_id,
            'nomor_pasal' => $request->nomor_pasal,
            'judul' => $request->judul,
        ]);

        $existingAyatIds = [];

        foreach ($request->ayat as $ayatData) {

            if (isset($ayatData['id'])) {

                $ayat = Ayat::find($ayatData['id']);

                $ayat->update([
                    'nomor_ayat' => $ayatData['nomor_ayat'],
                    'isi' => $ayatData['isi'],
                ]);

                $existingAyatIds[] = $ayat->id;

            } else {

                $ayat = Ayat::create([
                    'pasal_id' => $pasal->id,
                    'nomor_ayat' => $ayatData['nomor_ayat'],
                    'isi' => $ayatData['isi'],
                ]);

                $existingAyatIds[] = $ayat->id;

            }

        }

        $pasal->ayats()->whereNotIn('id', $existingAyatIds)->delete();

        return redirect()->route('pasal.index')
            ->with('success', 'Pasal berhasil diperbarui');

    }


    public function destroy($id)
    {
        Pasal::findOrFail($id)->delete();

        return back()->with('success', 'Pasal berhasil dihapus');
    }

}