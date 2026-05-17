<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\Guarantor;
use App\Models\Pasal;
use App\Models\PBDewasa;

class LitmasController extends Controller
{
    /**
     * Menampilkan daftar Litmas + tabel data tersimpan
     */
    public function index(Request $request)
    {
        $jenis   = $request->query('jenis');   // anak / dewasa / awal / null
        $search  = $request->query('search');
        $perPage = $request->query('per_page', 10);

        $user = Auth::user();

        // Query data litmas tersimpan (PBDewasa)
        $query = PBDewasa::with(['client', 'guarantor'])->latest();

        // User biasa hanya lihat litmas miliknya sendiri
        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $query->where('user_id', $user->id);
        }

        // Search: nama klien atau perkara
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($q2) use ($search) {
                    $q2->where('nama', 'like', '%' . $search . '%');
                })
                ->orWhere('perkara', 'like', '%' . $search . '%');
            });
        }

        $litmasList = $query->paginate($perPage)->withQueryString();

        return view('litmas.index', compact('jenis', 'litmasList', 'perPage'));
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
        $request->validate([
            'jenis_litmas' => 'required|string',
        ]);

        return redirect()
            ->route('litmas.index')
            ->with('success', 'Litmas berhasil dibuat sebagai draft');
    }

    /**
     * Menampilkan detail Litmas
     */
    public function show($id)
    {
        return view('litmas.show', compact('id'));
    }

    /**
     * Menampilkan form edit Litmas (PBDewasa)
     */
    public function edit($id)
    {
        $litmas = PBDewasa::with([
            'client', 'user', 'guarantor', 'guarantors',
            'klasifikasiHukum', 'families',
        ])->findOrFail($id);

        $user     = Auth::user();
        $pasals   = Pasal::with('klasifikasiHukum')->get();

        $clients  = Client::query();
        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $clients->where('user_id', $user->id);
        }
        $clients = $clients->get();

        // Sesuaikan jenis & kategori jika nanti ada kolom di model
        $jenis    = 'dewasa';
        $kategori = 'pembebasan_bersyarat';

        return view('litmas.form_PBDewasa', compact(
            'litmas', 'clients', 'pasals', 'jenis', 'kategori', 'user'
        ));
    }

    /**
     * Update data Litmas (PBDewasa)
     * — method ini dipanggil oleh route PUT /litmas/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id'    => 'required',
            'guarantor_id' => 'required',
            'perkara'      => 'required',
        ]);

        $litmas = PBDewasa::findOrFail($id);
        $client = Client::findOrFail($request->client_id);

        $litmas->update([
            // ── RELASI ──
            'client_id'    => $request->client_id,
            'user_id'      => $client->user_id,
            'guarantor_id' => $request->guarantor_id,

            // ── STEP 1 : NOTA DINAS ──
            'no_nota_dinas'      => $request->no_nota_dinas,
            'tanggal_nota_dinas' => $request->tgl_nota_dinas,
            'asal_surat_rujukan' => $request->kepada,
            'no_surat_rujukan'   => $request->no_surat,
            'tgl_surat_rujukan'  => $request->tanggal_surat,
            'no_reg_rutan'       => $request->no_register,
            'perkara'            => $request->perkara,
            'no_litmas'          => $request->no_litmas,

            // ── STEP 2 : PK ──
            'nip'     => $request->nip,
            'jabatan' => $request->jabatan,

            // ── STEP 2 : IDENTITAS KLIEN ──
            'tanggal_studi_literatur'    => $request->tanggal_wawancara,
            'saksi'                      => $request->sumber_informasi,
            'no_putusan_pengadilan'      => $request->no_putusan_pengadilan,
            'tanggal_putusan_pengadilan' => $request->tgl_putusan_pengadilan,
            'lama_pidana_denda'          => $request->lama_pidana,

            // ── STEP 2 : KONDISI PENJAMIN ──
            'perkawinan_penjamin'        => $request->penjamin_perkawinan,
            'relasi_keluarga_penjamin'   => $request->penjamin_relasi_keluarga,
            'relasi_masyarakat_penjamin' => $request->penjamin_relasi_masyarakat,
            'pekerjaan_penjamin'         => $request->penjamin_pekerjaan,
            'kondisi_rumah_penjamin'     => $request->penjamin_rumah,

            // ── STEP 3 : RIWAYAT ──
            'riwayat_kelahiran'    => $request->riwayat_kelahiran,
            'riwayat_pertumbuhan'  => $request->riwayat_pertumbuhan,
            'riwayat_perkembangan' => $request->riwayat_perkembangan,

            // ── STEP 3 : PENDIDIKAN ──
            'pendidikan_keluarga'  => $request->pendidikan_keluarga,
            'pendidikan_formal'    => $request->pendidikan_formal,
            'pendidikan_nonformal' => $request->pendidikan_nonformal,

            // ── STEP 3 : TINGKAH LAKU ──
            'bakat_potensi'       => $request->bakat_potensi,
            'relasi_keluarga'     => $request->relasi_sosial,
            'ketaatan_agama'      => $request->ketaatan_agama,
            'kebiasaan_baik'      => $request->kebiasaan_baik,
            'kebiasaan_buruk'     => $request->kebiasaan_buruk,
            'sikap_bekerja'       => $request->sikap_kerja,
            'riwayat_pelanggaran' => $request->riwayat_hukum,
            'riwayat_napza'       => $request->riwayat_zat,
            'riwayat_perkawinan'  => $request->riwayat_perkawinan,

            // ── STEP 3 : KONDISI LINGKUNGAN ──
            'relasi_masyarakat_klien'       => $request->lingkungan_relasi,
            'kondisi_lingkungan_klien'      => $request->lingkungan_kondisi,
            'profesi_masyarakat'            => $request->lingkungan_profesi,
            'eknomi_masyarakat'             => $request->lingkungan_strata,
            'tingkat_pendidikan_masyarakat' => $request->lingkungan_pendidikan,
            'kehidupan_masyarakat'          => $request->kepedulian_masyarakat,
            'kegiatan_pendidikan'           => $request->kepedulian_pendidikan,
            'kegiatan_keagamaan'            => $request->kepedulian_agama,
            'penegak_hukum'                 => $request->kepedulian_hukum,

            // ── STEP 3 : TINDAK PIDANA ──
            'latar_belakang'    => $request->pidana_latar,
            'kronologis'        => $request->pidana_kronologis,
            'keadaan_korban'    => $request->pidana_korban,
            'dampak_klien'      => $request->akibat_klien,
            'dampak_keluarga'   => $request->akibat_keluarga,
            'dampak_masyarakat' => $request->akibat_masyarakat,

            // ── STEP 3 : TANGGAPAN ──
            'tanggapan_klien'      => $request->tanggapan_klien,
            'tanggapan_keluarga'   => $request->tanggapan_keluarga,
            'tanggapan_masyarakat' => $request->tanggapan_masyarakat,
            'tanggapan_pemerintah' => $request->tanggapan_pemerintah,

            // ── STEP 3 : EVALUASI PEMBINAAN ──
            'program_admisi'      => $request->evaluasi_admisi,
            '1/3_pidana'          => $request->tgl_sepertiga,
            '1/2_pidana'          => $request->tgl_setengah,
            '2/3_pidana'          => $request->tgl_duapertiga,
            'program_kepribadian' => $request->pembinaan_kepribadian,
            'program_kemandirian' => $request->pembinaan_kemandirian,
            'warga_binaan'        => $request->relasi_wbp,
            'petugas'             => $request->relasi_petugas,
            'keluarga'            => $request->relasi_keluarga,
            'masyarakat'          => $request->relasi_masyarakat,

            // ── STEP 3 : ASESMEN & ANALISIS ──
            'rekomendasi_asesmen'   => $request->hasil_asesmen,
            'sikap_klien_pembinaan' => $request->analisis_resiko,
            'hasil_setelah_program' => $request->analisis_hasil,
            'kesiapan_masyarakat'   => $request->analisis_penerimaan,

            // ── STEP 3 : KESIMPULAN ──
            'kesimpulan'      => $request->kesimpulan,
            'tgl_rekomendasi' => $request->tgl_rekomendasi,
            'rekomendasi'     => $request->rekomendasi,
        ]);

        // Update klasifikasi hukum
        $litmas->klasifikasiHukum()->sync($request->pasal_id ?? []);

        // Update keluarga: hapus lama, simpan baru
        $litmas->families()->delete();
        $keluarga = $request->input('keluarga', []);
        foreach ($keluarga['nama'] ?? [] as $i => $nama) {
            if (!$nama) continue;
            \App\Models\family::create([
                'p_b_dewasa_id' => $litmas->id,
                'client_id'     => $litmas->client_id,
                'nama'          => $nama,
                'jk'            => $keluarga['jk'][$i]        ?? null,
                'usia'          => $keluarga['usia'][$i]       ?? null,
                'pendidikan'    => $keluarga['pendidikan'][$i] ?? null,
                'pekerjaan'     => $keluarga['pekerjaan'][$i]  ?? null,
                'keterangan'    => $keluarga['ket'][$i]        ?? null,
            ]);
        }

        return redirect()->route('export.preview', $litmas->id)
            ->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Menghapus Litmas (PBDewasa)
     */
    public function destroy($id)
    {
        $litmas = PBDewasa::findOrFail($id);

        // Hapus relasi dulu sebelum hapus litmas
        $litmas->families()->delete();
        $litmas->klasifikasiHukum()->detach();
        $litmas->delete();

        return redirect()->route('litmas.index')
            ->with('success', 'Data litmas berhasil dihapus');
    }

    /**
     * Menampilkan form Litmas berdasarkan jenis & kategori
     */
    public function form(Request $request)
    {
        $jenis    = $request->jenis_litmas;
        $kategori = $request->kategori;
        $user     = Auth::user();

        // CLIENT
        $clients = Client::query();
        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $clients->where('user_id', $user->id);
        }
        $clients = $clients->get();

        // PENJAMIN
        $penjamins = Guarantor::query();
        if (!$user->hasAnyRole(['admin', 'superuser'])) {
            $penjamins->where('user_id', $user->id);
        }
        $penjamins = $penjamins->get();

        // PASAL
        $pasals = Pasal::with('klasifikasiHukum')->get();

        // VIEW DINAMIS
        $view = 'litmas.form_default';

        if ($jenis == 'dewasa' && $kategori == 'pembebasan_bersyarat') {
            $view = 'litmas.form_PBDewasa';
        } elseif ($jenis == 'dewasa' && $kategori == 'cuti_bersyarat') {
            $view = 'litmas.form_CBDewasa';
        } elseif ($jenis == 'anak' && $kategori == 'pembebasan_bersyarat') {
            $view = 'litmas.form_PBAnak';
        } elseif ($jenis == 'anak' && $kategori == 'cuti_bersyarat') {
            $view = 'litmas.form_CBAnak';
        }

        return view($view, compact('jenis', 'kategori', 'clients', 'penjamins', 'pasals', 'user'));
    }

    /**
     * Preview Litmas
     */
    public function preview(Request $request)
    {
        $client  = Client::find($request->client_id);
        $penjamin = Guarantor::find($request->penjamin_id);
        $pasal   = Pasal::with('ayats', 'klasifikasiHukum')->find($request->pasal_id);

        return view('litmas.preview', compact('client', 'penjamin', 'pasal'));
    }

    /**
     * AJAX — ambil data keluarga/penjamin berdasarkan client
     */
    public function getKeluarga($clientId)
    {
        $data = Guarantor::where('client_id', $clientId)->get();

        $data = $data->map(function ($item) {
            $hub = strtolower($item->hubungan_keluarga);

            if (str_contains($hub, 'ayah')) {
                $item->jenis = 'ayah';
            } elseif (str_contains($hub, 'ibu')) {
                $item->jenis = 'ibu';
            } else {
                $item->jenis = 'penjamin';
            }

            return $item;
        });

        return response()->json($data);
    }
}