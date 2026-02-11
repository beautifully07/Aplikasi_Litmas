<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * INDEX
     * - Admin & Superuser: lihat semua client
     * - User: hanya client miliknya
     */
    public function index(Request $request)
    {
        $query = Client::with('user');

        // Role user → filter client miliknya
        if (!auth()->user()->hasAnyRole(['admin', 'superuser'])) {
            $query->where('user_id', auth()->id());
        }

        // 🔍 Search nama client
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 10);

        $clients = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    /**
     * CREATE
     * - Admin & Superuser bisa memilih user (petugas)
     * - User biasa otomatis dirinya sendiri
     */
    public function create()
    {
        $users = [];

        if (auth()->user()->hasAnyRole(['admin', 'superuser'])) {
            $users = User::role('user')->get();
        }

        return view('clients.create', compact('users'));
    }

    /**
     * STORE
     * Semua role bisa tambah client
     */
    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required|string',
            'no_register' => 'required|unique:clients,no_register',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date_format:d-m-Y',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'nullable|string',
            'status_perkawinan' => 'nullable|string',
            'suku' => 'nullable|string',
            'kebangsaan' => 'nullable|string',
            'kewarganegaraan' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'ciri_khusus' => 'nullable|string',
        ];

        // Admin wajib memilih user
        if (auth()->user()->hasAnyRole(['admin', 'superuser'])) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        // User biasa → otomatis dirinya sendiri
        if (auth()->user()->hasRole('user')) {
            $validated['user_id'] = auth()->id();
        }

        // 🔹 Parsing tanggal lahir
        $validated['tanggal_lahir'] = $validated['tanggal_lahir']
            ? Carbon::createFromFormat('d-m-Y', $validated['tanggal_lahir'])->format('Y-m-d')
            : null;

        // 🔹 Hitung usia di backend
        $validated['usia'] = $validated['tanggal_lahir']
            ? Carbon::parse($validated['tanggal_lahir'])->age
            : null;

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

        return view('clients.show', compact('client'));
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

        $rules = [
            'nama' => 'required|string',
            'no_register' => 'required|unique:clients,no_register,' . $client->id,
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date_format:d-m-Y',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'nullable|string',
            'status_perkawinan' => 'nullable|string',
            'suku' => 'nullable|string',
            'kebangsaan' => 'nullable|string',
            'kewarganegaraan' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'ciri_khusus' => 'nullable|string',
        ];

        // Admin boleh mengganti petugas
        if (auth()->user()->hasAnyRole(['admin', 'superuser'])) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        // 🔹 Parsing tanggal lahir
        $validated['tanggal_lahir'] = $validated['tanggal_lahir']
            ? Carbon::createFromFormat('d-m-Y', $validated['tanggal_lahir'])->format('Y-m-d')
            : null;

        // 🔹 Hitung ulang usia
        $validated['usia'] = $validated['tanggal_lahir']
            ? Carbon::parse($validated['tanggal_lahir'])->age
            : null;

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
