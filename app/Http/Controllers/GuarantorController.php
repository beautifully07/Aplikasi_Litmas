<?php

namespace App\Http\Controllers;

use App\Models\Guarantor;
use App\Models\Client;
use Illuminate\Http\Request;

class GuarantorController extends Controller
{
    /**
     * Ambil data klien untuk dropdown form
     */
    public function create()
    {
        $clients = Client::select('id', 'nama')->orderBy('nama')->get();

        return response()->json([
            'clients' => $clients
        ]);
    }

    /**
     * Simpan penjamin milik klien
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'no_kk' => 'required|numeric',
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string',
            'suku' => 'required|string',
            'kewarganegaraan' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'pekerjaan' => 'required|string',
            'alamat' => 'required|string',
            'hubungan_keluarga' => 'required|in:Ayah Kandung,Ibu Kandung,Saudara Kandung,Suami,Istri',
            'usia' => 'nullable|string'
        ]);

        // otomatis terhubung ke klien
        $client = Client::findOrFail($validated['client_id']);

        $guarantor = $client->guarantors()->create($validated);

        return response()->json([
            'message' => 'Penjamin berhasil ditambahkan ke klien',
            'client' => $client->nama,
            'data' => $guarantor->load('client')
        ], 201);
    }

    /**
     * Lihat semua penjamin milik klien tertentu
     */
    public function byClient($client_id)
    {
        $client = Client::with('guarantors')->findOrFail($client_id);

        return response()->json([
            'client' => $client->nama,
            'guarantors' => $client->guarantors
        ]);
    }

    /**
     * Detail penjamin
     */
    public function show($id)
    {
        return Guarantor::with('client')->findOrFail($id);
    }
}