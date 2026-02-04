<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * INDEX
     * User → lihat client miliknya
     * Admin & Superuser → lihat semua client
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'superuser'])) {
            $clients = Client::latest()->get();
        } else {
            $clients = Client::where('user_id', $user->id)
                             ->latest()
                             ->get();
        }

        return view('clients.index', [
            'title' => 'Data Client',
            'clients' => $clients
        ]);
    }

    /**
     * STORE
     * Semua role bisa tambah
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_register' => 'required|string|unique:clients,no_register',
            'jenis_kelamin' => 'required|in:L,P',

            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',

            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'suku' => 'nullable|string|max:100',
            'kebangsaan' => 'nullable|string|max:100',
            'kewarganegaraan' => 'nullable|in:WNI,WNA',

            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
            'usia' => 'nullable|string'
        ]);

        // user login otomatis jadi pemilik client
        $validated['user_id'] = auth()->id();

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Data client berhasil disimpan');
    }

    /**
     * SHOW
     * User hanya boleh lihat client miliknya
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);

        if (
            auth()->user()->hasRole('user') &&
            $client->user_id !== auth()->id()
        ) {
            abort(403);
        }

        return view('clients.show', [
            'title' => 'Detail Client',
            'client' => $client
        ]);
    }

    /**
     * UPDATE
     * User hanya boleh update client miliknya
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        if (
            auth()->user()->hasRole('user') &&
            $client->user_id !== auth()->id()
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_register' => 'required|unique:clients,no_register,' . $id,
            'jenis_kelamin' => 'required|in:L,P',

            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',

            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'suku' => 'nullable|string|max:100',
            'kebangsaan' => 'nullable|string|max:100',
            'kewarganegaraan' => 'nullable|in:WNI,WNA',

            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pendidikan' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Data client berhasil diperbarui');
    }

    /**
     * DESTROY
     * Hanya Admin & Superuser
     */
    public function destroy($id)
    {
        abort_unless(
            auth()->user()->hasAnyRole(['admin', 'superuser']),
            403
        );

        Client::destroy($id);

        return redirect()->route('clients.index')
            ->with('success', 'Data client berhasil dihapus');
    }
}
