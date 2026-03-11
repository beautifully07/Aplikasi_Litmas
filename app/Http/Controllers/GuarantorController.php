<?php

namespace App\Http\Controllers;

use App\Models\Guarantor;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GuarantorController extends Controller
{

    /**
     * INDEX
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->get('per_page', 10);

        $penjamins = Guarantor::with('client')

            // Batasi data jika bukan admin / superuser
            ->when(!$user->hasRole(['admin', 'superuser']), function ($q) use ($user) {
                $q->whereHas('client', function ($qc) use ($user) {
                    $qc->where('user_id', $user->id);
                });
            })

            // SEARCH
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%$search%")
                        ->orWhereHas('client', function ($qc) use ($search) {
                            $qc->where('nama', 'like', "%$search%");
                        });
                });
            })

            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('penjamin.index', compact('penjamins', 'perPage'));
    }


    /**
     * FORM TAMBAH
     */
    public function create()
    {
        $user = auth()->user();

        // ADMIN & SUPERUSER → semua client
        if ($user->hasRole(['admin', 'superuser'])) {

            $clients = Client::orderBy('nama')->get();

        } else {

            // USER → hanya client miliknya
            $clients = Client::where('user_id', $user->id)
                ->orderBy('nama')
                ->get();
        }

        return view('penjamin.create', compact('clients'));
    }


    /**
     * SIMPAN PENJAMIN
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
        $user = auth()->user();

        // Jika user biasa → pastikan client miliknya
        if (!$user->hasRole(['admin', 'superuser'])) {

            if ($client->user_id !== $user->id) {
                abort(403, 'Tidak berhak menambahkan penjamin untuk client ini');
            }
        }

        foreach ($request->penjamin as $i => $data) {

            // Validasi umur minimal 18
            $umur = Carbon::parse($data['tanggal_lahir'])->age;

            if ($umur < 18) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "penjamin.$i.tanggal_lahir" => "Penjamin minimal berusia 18 tahun"
                    ]);
            }

            Guarantor::create([
                'user_id' => $user->id,
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
     * FORM EDIT
     */
    public function edit(Guarantor $penjamin)
    {
        $user = auth()->user();

        if (
            !$user->hasRole(['admin', 'superuser']) &&
            $penjamin->client->user_id !== $user->id
        ) {
            abort(403);
        }

        $clients = $user->hasRole(['admin', 'superuser'])
            ? Client::orderBy('nama')->get()
            : Client::where('user_id', $user->id)->orderBy('nama')->get();

        return view('penjamin.edit', compact('penjamin', 'clients'));
    }


    /**
     * UPDATE
     */
    public function update(Request $request, Guarantor $penjamin)
    {
        $user = auth()->user();

        if (
            !$user->hasRole(['admin', 'superuser']) &&
            $penjamin->client->user_id !== $user->id
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
     * DETAIL PENJAMIN
     */
    public function show(Guarantor $penjamin)
    {
        $penjamin->load('client');

        return view('penjamin.show', compact('penjamin'));
    }


    /**
     * LIST PENJAMIN PER CLIENT
     */
    public function byClient($client_id)
    {
        $client = Client::with('guarantors')->findOrFail($client_id);

        return view('penjamin.partials.table', compact('client'));
    }


    /**
     * HAPUS
     */
    public function destroy($id)
    {
        $penjamin = Guarantor::findOrFail($id);

        $penjamin->delete();

        return redirect()
            ->route('penjamin.index')
            ->with('success', 'Data penjamin berhasil dihapus');
    }
}