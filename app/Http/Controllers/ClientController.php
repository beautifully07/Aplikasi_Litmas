<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Client::with('user');

        // User biasa → hanya client miliknya
        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $query->where('user_id', $user->id);
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

        return view('clients.index', compact('clients','perPage'));
    }

    /**
     * CREATE
     * - Admin & Superuser: pilih user (petugas)
     * - User biasa: otomatis dirinya sendiri
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $users = collect();

        if ($user->hasAnyRole(['admin', 'superuser'])) {
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = [
            'nama'             => 'required|string|max:255',
            'no_register'      => 'required|unique:clients,no_register',
            'tempat_lahir'     => 'nullable|string|max:255',
            'tanggal_lahir'    => 'nullable|date_format:d-m-Y',
            'jenis_kelamin'    => 'required|in:L,P',
            'agama'            => 'nullable|string|max:100',
            'status_perkawinan'=> 'nullable|string|max:100',
            'suku'             => 'nullable|string|max:100',
            'kebangsaan'       => 'nullable|string|max:100',
            'kewarganegaraan'  => 'nullable|string|max:100',
            'pendidikan'       => 'nullable|string|max:100',
            'pekerjaan'        => 'nullable|string|max:255',
            'alamat'           => 'nullable|string',
            'ciri_khusus'      => 'nullable|string',
        ];

        // Admin & superuser wajib pilih user
        if ($user->hasAnyRole(['admin', 'superuser'])) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        // User biasa → otomatis dirinya sendiri
        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $validated['user_id'] = $user->id;
        }

        // 🔹 Parsing tanggal lahir
        $validated['tanggal_lahir'] = $validated['tanggal_lahir']
            ? Carbon::createFromFormat('d-m-Y', $validated['tanggal_lahir'])->format('Y-m-d')
            : null;

        // 🔹 Hitung usia
        $validated['usia'] = $validated['tanggal_lahir']
            ? Carbon::parse($validated['tanggal_lahir'])->age
            : null;

        Client::create($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data client berhasil disimpan');
    }

    /**
     * SHOW
     * User hanya boleh lihat client miliknya
     */
    public function show($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $client = Client::with('user')->findOrFail($id);

        if (
            !$user->hasAnyRole(['admin', 'superuser']) &&
            $client->user_id !== $user->id
        ) {
            abort(403);
        }

        return view('clients.show', compact('client'));
    }

    /**
     * FORM EDIT CLIENT  ✅ INI YANG SEBELUMNYA BELUM ADA
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * UPDATE
     * User hanya boleh update client miliknya
     */
    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $client = Client::findOrFail($id);

        if (
            !$user->hasAnyRole(['admin', 'superuser']) &&
            $client->user_id !== $user->id
        ) {
            abort(403);
        }

        $rules = [
            'nama'              => 'required|string|max:255',
            'no_register'       => 'required|unique:clients,no_register,' . $client->id,
            'tempat_lahir'      => 'nullable|string|max:255',
            'tanggal_lahir'     => 'nullable|date_format:d-m-Y',
            'jenis_kelamin'     => 'required|in:L,P',
            'agama'             => 'nullable|string|max:100',
            'status_perkawinan' => 'nullable|string|max:100',
            'suku'              => 'nullable|string|max:100',
            'kebangsaan'        => 'nullable|string|max:100',
            'kewarganegaraan'   => 'nullable|string|max:100',
            'pendidikan'        => 'nullable|string|max:100',
            'pekerjaan'         => 'nullable|string|max:255',
            'alamat'            => 'nullable|string',
            'ciri_khusus'       => 'nullable|string',
        ];

        if ($user->hasAnyRole(['admin', 'superuser'])) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        $validated['tanggal_lahir'] = $validated['tanggal_lahir']
            ? Carbon::createFromFormat('d-m-Y', $validated['tanggal_lahir'])->format('Y-m-d')
            : null;

        $validated['usia'] = $validated['tanggal_lahir']
            ? Carbon::parse($validated['tanggal_lahir'])->age
            : null;

        $client->update($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data client berhasil diperbarui');
    }

    /**
     * DESTROY
     * Hanya Admin & Superuser
     */
    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless(
            $user->hasAnyRole(['admin', 'superuser']),
            403
        );

        Client::destroy($id);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data client berhasil dihapus');
    }
}
