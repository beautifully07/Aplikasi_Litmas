<?php

namespace App\Http\Controllers;
use App\Models\Pasal;
use App\Models\Ayat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PasalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Pasal::with('user');

        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        
      $perPage = $request->get('per_page', 10);

    $pasals = Pasal::with('ayats')
        ->when($request->search, function ($query) use ($request) {
            $query->where('nomor_pasal', 'like', '%' . $request->search . '%')
                  ->orWhere('judul', 'like', '%' . $request->search . '%');
        })
        ->paginate($perPage)
        ->withQueryString();

    return view('pasal.index', compact('pasals', 'perPage'));
    }

    public function create()
    {
        return view('pasal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pasal' => 'required',
            'ayat.*.nomor' => 'required',
            'ayat.*.isi' => 'required',
        ]);

        $pasal = Pasal::create([
            'nomor_pasal' => $request->nomor_pasal,
            'judul' => $request->judul,
        ]);

        foreach ($request->ayat as $ayat) {
            Ayat::create([
                'pasal_id' => $pasal->id,
                'nomor_ayat' => $ayat['nomor'],
                'isi' => $ayat['isi'],
            ]);
        }

        return redirect()->route('pasal.index')
            ->with('success', 'Pasal berhasil ditambahkan');
    }

    // ✅ EDIT
    public function edit($id)
    {
        $pasal = Pasal::with('ayats')->findOrFail($id);
        return view('pasal.edit', compact('pasal'));
    }

    // ✅ UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'nomor_pasal' => 'required',
            'ayat.*.nomor' => 'required',
            'ayat.*.isi' => 'required',
        ]);

        $pasal = Pasal::findOrFail($id);

        $pasal->update([
            'nomor_pasal' => $request->nomor_pasal,
            'judul' => $request->judul,
        ]);

        $existingAyatIds = [];

        foreach ($request->ayat as $ayatData) {

            // Jika ada ID → update ayat lama
            if (isset($ayatData['id'])) {
                $ayat = Ayat::find($ayatData['id']);
                $ayat->update([
                    'nomor_ayat' => $ayatData['nomor'],
                    'isi' => $ayatData['isi'],
                ]);
                $existingAyatIds[] = $ayat->id;
            } 
            // Jika tidak ada ID → ayat baru
            else {
                $ayat = Ayat::create([
                    'pasal_id' => $pasal->id,
                    'nomor_ayat' => $ayatData['nomor'],
                    'isi' => $ayatData['isi'],
                ]);
                $existingAyatIds[] = $ayat->id;
            }
        }

        // Hapus ayat yang tidak ada di form
        $pasal->ayats()->whereNotIn('id', $existingAyatIds)->delete();

        return redirect()->route('pasal.index')
            ->with('success', 'Pasal berhasil diperbarui');
    }

    public function show($id)
    {
        $pasal = Pasal::with('ayats')->findOrFail($id);
        return view('pasal.show', compact('pasal'));
    }

    public function destroy($id)
    {
        Pasal::findOrFail($id)->delete();
        return back()->with('success', 'Pasal berhasil dihapus');
    }
}