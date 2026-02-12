<?php

namespace App\Http\Controllers;

use App\Models\Guarantor;
use Illuminate\Http\Request;

class GuarantorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'no_kk' => 'required|integer',
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

        $guarantor = Guarantor::create($validated);

        return response()->json([
            'message' => 'Guarantor successfully created',
            'data' => $guarantor
        ], 201);
    }

    public function show($id)
    {
        return Guarantor::with('client')->findOrFail($id);
    }
}

