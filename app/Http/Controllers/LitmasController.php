<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\Guarantor;
use App\Models\Pasal;

class LitmasController extends Controller
{
    /**
     * Menampilkan daftar Litmas
     * (Dashboard / Arsip Litmas)
     */
    public function index(Request $request)
{
    $jenis = $request->jenis; // ambil dari URL ?jenis=anak

    return view('litmas.index', compact('jenis'));
}

    /**
     * Menampilkan halaman pilih jenis Litmas
     */
    public function create()
    {
        return view('litmas.create');
    }

    /**
     * Menyimpan Litmas baru (Draft)
     */
    public function store(Request $request)
    {
        // contoh validasi sederhana
        $request->validate([
            'jenis_litmas' => 'required|string',
        ]);

        // contoh simpan (nanti diganti model + database)
        /*
        $litmas = Litmas::create([
            'jenis_litmas' => $request->jenis_litmas,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);
        */

        // sementara redirect saja
        return redirect()
            ->route('litmas.index')
            ->with('success', 'Litmas berhasil dibuat sebagai draft');
    }

    /**
     * Menampilkan detail Litmas
     */
    public function show($id)
    {
        // $litmas = Litmas::findOrFail($id);

        return view('litmas.show', compact('id'));
    }

    /**
     * Menampilkan form edit Litmas
     */
    public function edit($id)
    {
        // $litmas = Litmas::findOrFail($id);

        return view('litmas.edit', compact('id'));
    }

    /**
     * Update data Litmas
     */
    public function update(Request $request, $id)
    {
        // validasi contoh
        $request->validate([
            'status' => 'required|string',
        ]);

        /*
        $litmas = Litmas::findOrFail($id);
        $litmas->update($request->all());
        */

        return redirect()
            ->route('litmas.index')
            ->with('success', 'Litmas berhasil diperbarui');
    }

    /**
     * Menghapus Litmas
     */
    public function destroy($id)
    {
        /*
        $litmas = Litmas::findOrFail($id);
        $litmas->delete();
        */

        return redirect()
            ->route('litmas.index')
            ->with('success', 'Litmas berhasil dihapus');
    }

public function form(Request $request)
{
    $jenis = $request->jenis_litmas;
    $kategori = $request->kategori;

    $user = Auth::user();

    // ======================
    // CLIENT
    // ======================
    $clients = Client::query();

    if (!$user->hasAnyRole(['admin', 'superuser'])) {
        $clients->where('user_id', $user->id);
    }

    $clients = $clients->get();

    // ======================
    // PENJAMIN
    // ======================
    $penjamins = Guarantor::query();

    if (!$user->hasAnyRole(['admin', 'superuser'])) {
        $penjamins->where('user_id', $user->id);
    }

    $penjamins = $penjamins->get();

    // ======================
    // PASAL
    // ======================
    $pasals = Pasal::with('klasifikasiHukum')->get();

    // ======================
    // VIEW DINAMIS
    // ======================
    $view = 'litmas.form_default'; // fallback

    if ($jenis == 'dewasa' && $kategori == 'pembebasan_bersyarat') {
        $view = 'litmas.form_PBDewasa';
    } elseif ($jenis == 'dewasa' && $kategori == 'cuti_bersyarat') {
        $view = 'litmas.form_CBDewasa';
    } elseif ($jenis == 'anak' && $kategori == 'pembebasan_bersyarat') {
        $view = 'litmas.form_PBAnak';
    } elseif ($jenis == 'anak' && $kategori == 'cuti_bersyarat') {
        $view = 'litmas.form_CBAnak';
    }

    return view($view, compact(
        'jenis',
        'kategori',
        'clients',
        'penjamins',
        'pasals',
        'user'
    ));
}

public function preview(Request $request)
{
    $client = Client::find($request->client_id);
    $penjamin = Guarantor::find($request->penjamin_id);
    $pasal = Pasal::with('ayats','klasifikasiHukum')->find($request->pasal_id);

    return view('litmas.preview', compact('client','penjamin','pasal'));
}

public function getKeluarga($clientId)
{
    $data = Guarantor::where('client_id', $clientId)
        ->select(
            'id',
            'nama',
            'jenis',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'bangsa',
            'suku',
            'kewarganegaraan',
            'pendidikan',
            'pekerjaan',
            'alamat',
            'hubungan'
        )
        ->get();

    return response()->json($data);
}
}
