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
     * Admin & Superuser → semua client
     * User → hanya miliknya
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Client::with('user');

        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 10);

        $clients = $query->latest()->paginate($perPage)->withQueryString();

        return view('clients.index', compact('clients','perPage'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        $user = Auth::user();
        $users = collect();

        if ($user->hasAnyRole(['admin', 'superuser'])) {
            $users = User::role('user')->orderBy('name')->get();
        }

        return view('clients.create', compact('users'));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
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

        if ($user->hasAnyRole(['admin', 'superuser'])) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $validated['user_id'] = $user->id;
        }

        $validated['tanggal_lahir'] = $validated['tanggal_lahir']
            ? Carbon::createFromFormat('d-m-Y', $validated['tanggal_lahir'])->format('Y-m-d')
            : null;

        $validated['usia'] = $validated['tanggal_lahir']
            ? Carbon::parse($validated['tanggal_lahir'])->age
            : null;

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Data client berhasil disimpan');
    }

    /**
     * SHOW
     */
    public function show(Client $client)
    {
        $user = Auth::user();

        if (
            !$user->hasAnyRole(['admin', 'superuser']) &&
            $client->user_id !== $user->id
        ) {
            abort(403);
        }

        $client->load('user');

        return view('clients.show', compact('client'));
    }

    /**
     * EDIT (FIX DROPDOWN PETUGAS)
     */
    public function edit(Client $client)
    {
        $user = Auth::user();

        if (
            !$user->hasAnyRole(['admin', 'superuser']) &&
            $client->user_id !== $user->id
        ) {
            abort(403);
        }

        $users = collect();

        if ($user->hasAnyRole(['admin', 'superuser'])) {
            $users = User::role('user')->orderBy('name')->get();
        }

        return view('clients.edit', compact('client','users'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, Client $client)
    {
        $user = Auth::user();

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

        // 🔒 User biasa tidak boleh ubah petugas
        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $validated['user_id'] = $client->user_id;
        }

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Data client berhasil diperbarui');
    }

    /**
     * DELETE
     */
    public function destroy(Client $client)
    {
        $user = Auth::user();

        abort_unless($user->hasAnyRole(['admin', 'superuser']), 403);

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Data client berhasil dihapus');
    }
}