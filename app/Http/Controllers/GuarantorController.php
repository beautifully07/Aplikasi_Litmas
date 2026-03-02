<?php

namespace App\Http\Controllers;

use App\Models\Guarantor;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GuarantorController extends Controller
{

    // Index
    public function index(Request $request)
    {
    $user = auth()->user();

    $penjamins = Guarantor::with('client')
        ->when(!$user->hasRole(['admin', 'superuser']), function ($q) use ($user) {
            $q->whereHas('client', function ($qc) use ($user) {
                $qc->where('user_id', $user->id);
            });
        })
        ->when($request->search, function ($q) use ($request) {
            $q->where('nama', 'like', '%' . $request->search . '%')
              ->orWhereHas('client', function ($qc) use ($request) {
                  $qc->where('nama', 'like', '%' . $request->search . '%');
              });
        })
        ->latest()
        ->paginate(10);

    return view('penjamin.index', compact('penjamins'));
    }

    /**
     * FORM TAMBAH
     * tampilkan halaman + dropdown klien
     */
    public function create()
    {
    $user = auth()->user();

    // ADMIN & SUPERUSER → lihat semua client
    if ($user->hasRole(['admin', 'superuser'])) {
        $clients = Client::orderBy('nama')->get();
    }
    // USER → hanya client miliknya
    else {
        $clients = Client::where('user_id', $user->id)
            ->orderBy('nama')
            ->get();
    }

    return view('penjamin.create', compact('clients'));
    }


    /**
     * SIMPAN BANYAK PENJAMIN SEKALIGUS
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'penjamin.*.nama' => 'required|string',
            'penjamin.*.tanggal_lahir' => 'required|date',
            'penjamin.*.hubungan_keluarga' => 'required|in:Ayah Kandung,Ibu Kandung,Saudara Kandung,Suami,Istri'
        ]);

        $client = Client::findOrFail($request->client_id);

        // Jika user biasa, pastikan client miliknya
        if (!in_array(auth()->user()->role, ['admin', 'superuser'])) {
            if ($client->user_id !== auth()->id()) {
                abort(403, 'Tidak berhak menambahkan penjamin untuk client ini');
            }
        }

        foreach ($request->penjamin as $i => $data) {

            // VALIDASI UMUR >= 18
            $umur = Carbon::parse($data['tanggal_lahir'])->age;

            if ($umur < 18) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "penjamin.$i.tanggal_lahir" => "Penjamin minimal berusia 18 tahun"
                    ]);
            }

            Guarantor::create([
                'user_id' => auth()->id(),
                'client_id' => $request->client_id,
                'no_kk' => $data['no_kk'] ?? null,
                'nama' => $data['nama'],
                'tempat_lahir' => $data['tempat_lahir'] ?? null,
                'tanggal_lahir' => $data['tanggal_lahir'],
                'agama' => $data['agama'] ?? null,
                'suku' => $data['suku'] ?? null,
                'kewarganegaraan' => $data['kewarganegaraan'] ?? null,
                'pendidikan_terakhir' => $data['pendidikan_terakhir'] ?? null,
                'pekerjaan' => $data['pekerjaan'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'hubungan_keluarga' => $data['hubungan_keluarga'],
            ]);
        }

        return redirect()
            ->route('penjamin.index')
            ->with('success', 'Data penjamin berhasil disimpan');
    }


    /**
     * FORM EDIT (1 klien → banyak penjamin)
     */
    public function edit(Guarantor $penjamin)
{
    $user = auth()->user();

    if (
        !$user->hasRole(['admin', 'superuser']) &&
        $penjamin->user_id !== $user->id
    ) {
        abort(403);
    }

    $clients = $user->hasRole(['admin', 'superuser'])
        ? Client::orderBy('nama')->get()
        : Client::where('user_id', $user->id)->orderBy('nama')->get();

    return view('penjamin.edit', compact('penjamin', 'clients'));
}


    /**
     * UPDATE MASSAL (create + update + delete)
     */
    public function update(Request $request, Guarantor $penjamin)
{
    $user = auth()->user();

    if (
        !$user->hasRole(['admin', 'superuser']) &&
        $penjamin->user_id !== $user->id
    ) {
        abort(403);
    }

    $request->validate([
        'client_id' => 'required|exists:clients,id',
        'nama' => 'required|string',
        'tanggal_lahir' => 'required|date',
        'hubungan_keluarga' => 'required|in:Ayah Kandung,Ibu Kandung,Saudara Kandung,Suami,Istri'
    ]);

    $penjamin->update([
        'client_id' => $request->client_id,
        'no_kk' => $request->no_kk,
        'nama' => $request->nama,
        'tempat_lahir' => $request->tempat_lahir,
        'tanggal_lahir' => $request->tanggal_lahir,
        'agama' => $request->agama,
        'suku' => $request->suku,
        'kewarganegaraan' => $request->kewarganegaraan,
        'pendidikan_terakhir' => $request->pendidikan_terakhir,
        'pekerjaan' => $request->pekerjaan,
        'alamat' => $request->alamat,
        'hubungan_keluarga' => $request->hubungan_keluarga,
    ]);

    return redirect()
        ->route('penjamin.index')
        ->with('success', 'Data penjamin berhasil diperbarui');
}


    /**
     * LIST PENJAMIN PER KLIEN (optional untuk AJAX tabel)
     */
    public function byClient($client_id)
    {
        $client = Client::with('guarantors')->findOrFail($client_id);
        return view('penjamin.partials.table', compact('client'));
    }


    /**
     * DETAIL PENJAMIN
     */
    public function show(Guarantor $penjamin)
{
    $penjamin->load('client'); // agar relasi aman dipakai

    return view('penjamin.show', compact('penjamin'));
}
}