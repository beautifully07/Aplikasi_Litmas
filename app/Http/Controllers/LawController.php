<?php

namespace App\Http\Controllers;

use App\Models\Law;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LawController extends Controller
{
     public function index($perkaraId)
    {
        return Law::where('perkara_id', $perkaraId)->get();
    }

    /**
     * Menyimpan dasar hukum (bisa banyak pasal sekaligus)
     */
    public function store(Request $request)
    {
        $request->validate([
            'perkara_id'        => 'required|exists:perkara,id',
            'jenis_peraturan'  => 'required|string',
            'nomor_peraturan'  => 'required|string',
            'tahun_peraturan'  => 'required|digits:4',
            'pasal'            => 'required|array',
            'pasal.*'          => 'required|string',
            'ayat'             => 'required|array',
            'ayat.*'           => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->pasal as $index => $pasal) {
                Law::create([
                    'perkara_id'       => $request->perkara_id,
                    'jenis_peraturan' => $request->jenis_peraturan,
                    'nomor_peraturan' => $request->nomor_peraturan,
                    'tahun_peraturan' => $request->tahun_peraturan,
                    'pasal'           => $pasal,
                    'ayat'            => $request->ayat[$index],
                ]);
            }
        });

    }

    //update data dasar hukum

    public function update(Request $request, $id)
    {
        $dasarHukum = Law::findOrFail($id);

        $request->validate([
            'pasal' => 'required|string',
            'ayat'  => 'required|string',
        ]);

        $dasarHukum->update($request->only(['pasal', 'ayat']));

        return response()->json([
            'message' => 'Dasar hukum berhasil diperbarui',
            'data'    => $dasarHukum
        ]);
    }

     /**
     * Hapus satu pasal
     */
    public function destroy($id)
    {
        Law::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Dasar hukum berhasil dihapus'
        ]);
    }
}