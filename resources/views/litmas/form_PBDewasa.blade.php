@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="bg-red-200 p-3 mb-3">
        {{ implode(', ', $errors->all()) }}
    </div>
@endif
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-6">
        Form Litmas {{ $jenis }} - {{ str_replace('_',' ', $kategori) }}
    </h2>

    <form action="{{ route('export.store') }}" method="POST">
        @csrf

{{-- =========================
    STEP 1: NOTA DINAS
========================= --}}
<div id="step-1">

    <h3 class="text-lg font-semibold mb-4">Nota Dinas</h3>

    <div class="grid grid-cols-2 gap-4">

        <input type="text" name="no_nota_dinas" class="input" placeholder="Nomor Nota Dinas">
        <input type="text" name="tanggal_nota_dinas" placeholder="Tanggal Nota Dinas"
                onfocus="this.type='date'" onblur="if(!this.value)this.type='text'" class="input">
        <input type="text" name="perihal" class="input" placeholder="Perihal">
        <input type="text" name="kepada" class="input" placeholder="Kepada">

        <input type="text" name="no_surat" class="input" placeholder="Nomor Surat Rujukan">
        <input type="text" name="tanggal_surat" placeholder="Tanggal Surat Rujukan"
                onfocus="this.type='date'" onblur="if(!this.value)this.type='text'" class="input">

        <input type="text" name="no_register" class="input" placeholder="No Register">
        <input type="text" name="perkara" class="input" placeholder="Perkara">

    </div>

    <div class="bg-white border rounded-lg p-4 mt-8 mb-8 w-full">

        <label class="font-semibold block mb-2">Dasar Hukum</label>

        <div id="dasar_hukum_wrapper" class="space-y-4 mt-4">

            <div class="flex gap-2 w-full">
                <select name="pasal_id[]" class="input w-full">
                    <option value="">-- Pilih Pasal --</option>

                    @foreach($pasals as $pasal)
                        <option value="{{ $pasal->id }}">
                            Pasal {{ $pasal->nomor_pasal }} -
                            {{ $pasal->klasifikasiHukum->nama_klasifikasi ?? '-' }}
                        </option>
                    @endforeach
                </select>

                <button type="button"
                    onclick="hapusDasarHukum(this)"
                    class="bg-red-500 text-white px-3 rounded">
                    🗑
                </button>
            </div>

    </div>

    <button type="button"
        onclick="tambahDasarHukum()"
        class="mt-3 bg-blue-600 text-white px-4 py-2 rounded text-sm">
        + Tambah Dasar Hukum
    </button>

</div>

    {{-- PILIH KLIEN --}}
    <div class="mt-4">
        <label>Nama Klien</label>
        <select name="client_id" id="client_id" class="input">
            <option value="">-- Pilih Klien --</option>

            @foreach($clients as $client)
            <option value="{{ $client->id }}"
                data-nama="{{ $client->nama }}"
                data-user="{{ $client->user->name ?? '' }}"
                data-tempat="{{ $client->tempat_lahir }}"
                data-tanggal="{{ $client->tanggal_lahir }}"
                data-alamat="{{ $client->alamat }}"
                data-jk="{{ $client->jenis_kelamin }}"
                data-status="{{ $client->status_perkawinan }}"
                data-agama="{{ $client->agama }}"
                data-pendidikan="{{ $client->pendidikan }}"
                data-pekerjaan="{{ $client->pekerjaan }}"
                data-ciri="{{ $client->ciri_khusus }}">
                {{ $client->nama }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- AUTO STEP 1 --}}
    <div class="grid grid-cols-2 gap-4 mt-4">

        <div>
            <label>TTL</label>
            <input type="text" id="ttl_1"
                class="input bg-gray-100" readonly>
        </div>

        <div>
            <label>Alamat</label>
            <input type="text" id="alamat_1"
                class="input bg-gray-100" readonly>
        </div>

    </div>

    <div class="mt-6 text-right">
        <button type="button" onclick="handleNext()" class="btn-blue">
            Next →
        </button>
    </div>

</div>


{{-- =========================
    STEP 2
========================= --}}
<div id="step-2" style="display:none;">

    <h3 class="text-lg font-semibold mb-4">Data Pembimbing Kemasyarakatan</h3>

    <div class="grid grid-cols-2 gap-4">

        <input type="text"
                id="nama_pk"
                class="input bg-gray-100"
                readonly>

        <input type="text" name="nip" class="input" placeholder="NIP">

        <input type="text" name="jabatan" class="input" placeholder="Jabatan">

    </div>

    <hr class="my-6">

    <h3 class="text-lg font-semibold mb-5 mt-4">Identitas Klien</h3>

    <div class="grid grid-cols-2 gap-4">

        <input type="text" id="nama_klien"
               class="input bg-gray-100" readonly>

        <input type="text" name="no_registrasi"
               class="input" placeholder="No Registrasi">
        
        <input type="text" name="tanggal_wawancara" placeholder="Tanggal Wawancara"
                onfocus="this.type='date'" onblur="if(!this.value)this.type='text'" class="input">

        <input type="text" name="sumber_informasi"
               class="input" placeholder="Sumber Informasi">

        <input type="text" name="no_putusan_pengadilan"
               class="input" placeholder="No Putusan Pengadilan">

        <input type="text" name="tgl_putusan_pengadilan"
               class="input" placeholder="Tanggal Putusan Pengadilan">

        <input type="text" name="lama_pidana"
               class="input" placeholder="Lama Pidana">

        <input type="text" id="ttl_2"
               class="input bg-gray-100" readonly>

        <input type="text" id="jenis_kelamin"
               class="input bg-gray-100" readonly>

        <input type="text" id="status"
               class="input bg-gray-100" readonly>

        <input type="text" id="agama"
               class="input bg-gray-100" readonly>

        <input type="text" id="pendidikan"
               class="input bg-gray-100" readonly>

        <input type="text" id="pekerjaan"
               class="input bg-gray-100" readonly>

        <textarea id="alamat_2"
                  class="input bg-gray-100 col-span-2"
                  readonly></textarea>

        <textarea id="ciri"
                  class="input bg-gray-100 col-span-2"
                  readonly></textarea>
    </div>

    <hr class="my-6">

    <h3 class="text-lg font-semibold mb-5 mt-4">Orang Tua Kandung / Penjamin</h3>

    {{-- ================= AYAH ================= --}}
    <h4 class="font-semibold mt-4 mb-2">Ayah</h4>

    <div class="grid grid-cols-2 gap-4">

        <div>
            <label>Nama Ayah</label>
            <select id="ayah_select" class="input">
                <option value="">-- Pilih Ayah --</option>
            </select>
        </div>

        <div>
            <label>TTL</label>
            <input type="text" id="ayah_ttl" class="input bg-gray-100" readonly>
        </div>

        <div><label>Agama</label><input type="text" id="ayah_agama" class="input bg-gray-100" readonly></div>
        <div><label>Suku</label><input type="text" id="ayah_suku" class="input bg-gray-100" readonly></div>
        <div><label>Kewarganegaraan</label><input type="text" id="ayah_warga" class="input bg-gray-100" readonly></div>
        <div><label>Pendidikan</label><input type="text" id="ayah_pendidikan" class="input bg-gray-100" readonly></div>
        <div><label>Pekerjaan</label><input type="text" id="ayah_pekerjaan" class="input bg-gray-100" readonly></div>

        <div class="col-span-2">
            <label>Alamat</label>
            <textarea id="ayah_alamat" class="input bg-gray-100" readonly></textarea>
        </div>

        <div>
            <label>Hubungan</label>
            <input type="text" id="ayah_hubungan" class="input bg-gray-100" readonly>
        </div>

    </div>


    {{-- ================= IBU ================= --}}
    <h4 class="font-semibold mt-6 mb-2">Ibu</h4>

    <div class="grid grid-cols-2 gap-4">

        <div>
            <label>Nama Ibu</label>
            <select id="ibu_select" class="input"></select>
        </div>

        <div><label>TTL</label><input type="text" id="ibu_ttl" class="input bg-gray-100" readonly></div>
        <div><label>Agama</label><input type="text" id="ibu_agama" class="input bg-gray-100" readonly></div>
        <div><label>Suku</label><input type="text" id="ibu_suku" class="input bg-gray-100" readonly></div>
        <div><label>Kewarganegaraan</label><input type="text" id="ibu_warga" class="input bg-gray-100" readonly></div>
        <div><label>Pendidikan</label><input type="text" id="ibu_pendidikan" class="input bg-gray-100" readonly></div>
        <div><label>Pekerjaan</label><input type="text" id="ibu_pekerjaan" class="input bg-gray-100" readonly></div>

        <div class="col-span-2">
            <label>Alamat</label>
            <textarea id="ibu_alamat" class="input bg-gray-100" readonly></textarea>
        </div>

        <div><label>Hubungan</label><input type="text" id="ibu_hubungan" class="input bg-gray-100" readonly></div>

    </div>


    {{-- ================= PENJAMIN ================= --}}
    <h4 class="font-semibold mt-6 mb-2">Penjamin</h4>

    <div class="grid grid-cols-2 gap-4">

        <div>
            <label>Nama Penjamin</label>
            <select name="guarantor_id" id="penjamin_select" class="input"></select>
        </div>

        <div><label>TTL</label><input type="text" id="penjamin_ttl" class="input bg-gray-100" readonly></div>
        <div><label>Agama</label><input type="text" id="penjamin_agama" class="input bg-gray-100" readonly></div>
        <div><label>Suku</label><input type="text" id="penjamin_suku" class="input bg-gray-100" readonly></div>
        <div><label>Kewarganegaraan</label><input type="text" id="penjamin_warga" class="input bg-gray-100" readonly></div>
        <div><label>Pendidikan</label><input type="text" id="penjamin_pendidikan" class="input bg-gray-100" readonly></div>
        <div><label>Pekerjaan</label><input type="text" id="penjamin_pekerjaan" class="input bg-gray-100" readonly></div>

        <div class="col-span-2">
            <label>Alamat</label>
            <textarea id="penjamin_alamat" class="input bg-gray-100" readonly></textarea>
        </div>

        <div><label>Hubungan</label><input type="text" id="penjamin_hubungan" class="input bg-gray-100" readonly></div>

    </div>

{{-- SUSUNAN KELUARGA --}}

<div class="bg-white shadow rounded-xl p-6 mt-6">

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-700">
            Susunan Keluarga
        </h3>

        <button type="button" onclick="tambahBaris()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm shadow">
            + Tambah Data
        </button>
    </div>

    <div class="overflow-x-auto">
    <table class="w-full table-fixed border border-gray-200">

        <thead class="bg-gray-100 text-xs">
            <tr>
                <th class="p-2 w-[10%]">Nama</th>
                <th class="p-2 w-[5%]">L/P</th>
                <th class="p-2 w-[5%]">Usia</th>
                <th class="p-2 w-[15%]">Pendidikan</th>
                <th class="p-2 w-[15%]">Pekerjaan</th>
                <th class="p-2 w-[26%]">Keterangan</th>
                <th class="p-2 w-[10%] text-center">Aksi</th>
            </tr>
        </thead>

        <tbody id="table_keluarga"></tbody>

    </table>

    {{-- NO KK --}}
<div class="mt-4">
    <label class="block font-semibold mb-1">No. Kartu Keluarga</label>
    <input type="text" name="no_kk"
           class="w-full border px-3 py-2 rounded"
           placeholder="Masukkan No KK">
</div>
</div>

</div>

    <div class="flex justify-between mt-6">
        <button type="button" onclick="prevStep(1)" class="btn-gray">
            ← Back
        </button>

        <button type="button" onclick="nextStep(3)" class="btn-blue">
            Next →
        </button>
    </div>

</div>

{{-- =========================
    STEP 3
========================= --}}
<div id="step-3" style="display:none;">

    <h3 class="text-lg font-semibold mb-4">
        RIWAYAT HIDUP DAN PERKEMBANGAN KLIEN
    </h3>

    {{-- ================= A ================= --}}
    <h4 class="font-semibold mb-3">
        A. Riwayat Kelahiran, Pertumbuhan dan Perkembangan Klien
    </h4>

    <div class="space-y-4 mb-6">

        <div>
            <label>1. Riwayat Kelahiran Klien</label>
            <textarea name="riwayat_kelahiran" class="input w-full h-24"></textarea>
        </div>

        <div>
            <label>2. Riwayat Pertumbuhan Klien</label>
            <textarea name="riwayat_pertumbuhan" class="input w-full h-24"></textarea>
        </div>

        <div>
            <label>3. Riwayat Perkembangan Klien</label>
            <textarea name="riwayat_perkembangan" class="input w-full h-24"></textarea>
        </div>

    </div>


    {{-- ================= B ================= --}}
    <h4 class="font-semibold mb-3">
        B. Riwayat Pendidikan Klien
    </h4>

    <div class="space-y-4 mb-6">

        <div>
            <label>1. Pendidikan dalam Keluarga</label>
            <textarea name="pendidikan_keluarga" class="input w-full h-24"></textarea>
        </div>

        <div>
            <label>2. Pendidikan Formal</label>
            <textarea name="pendidikan_formal" class="input w-full h-24"></textarea>
        </div>

        <div>
            <label>3. Pendidikan Non Formal</label>
            <textarea name="pendidikan_nonformal" class="input w-full h-24"></textarea>
        </div>

    </div>


    {{-- ================= C ================= --}}
    <h4 class="font-semibold mb-3">
        C. Riwayat Tingkah Laku
    </h4>

    <div class="space-y-4 mb-6">

        <div>
            <label>1. Bakat dan Potensi Klien</label>
            <textarea name="bakat_potensi" class="input w-full h-20"></textarea>
        </div>

        <div>
            <label>2. Relasi Sosial dengan Orang Tua dan Keluarga</label>
            <textarea name="relasi_sosial" class="input w-full h-20"></textarea>
        </div>

        <div>
            <label>3. Ketaatan dalam Beragama</label>
            <textarea name="ketaatan_agama" class="input w-full h-20"></textarea>
        </div>

        <div>
            <label>4. Kebiasaan Baik</label>
            <textarea name="kebiasaan_baik" class="input w-full h-20"></textarea>
        </div>

        <div>
            <label>5. Kebiasaan Buruk</label>
            <textarea name="kebiasaan_buruk" class="input w-full h-20"></textarea>
        </div>

        <div>
            <label>6. Sikap dalam Bekerja</label>
            <textarea name="sikap_kerja" class="input w-full h-20"></textarea>
        </div>

        <div>
            <label>7. Riwayat Pelanggaran Hukum</label>
            <textarea name="riwayat_hukum" class="input w-full h-20"></textarea>
        </div>

        <div>
            <label>8. Riwayat Rokok, Napza, dan Alkohol</label>
            <textarea name="riwayat_zat" class="input w-full h-20"></textarea>
        </div>

    </div>


    {{-- ================= D ================= --}}
    <h4 class="font-semibold mb-3">
        D. Riwayat Perkawinan Klien
    </h4>

    <div class="mb-6">
        <textarea name="riwayat_perkawinan" class="input w-full h-24"></textarea>
    </div>


{{-- ================= II ================= --}}
<h3 class="text-lg font-semibold mt-8 mb-4">
    II. KONDISI SOSIAL LINGKUNGAN TEMPAT TINGGAL PENJAMIN
</h3>

<div class="space-y-4 mb-6">

    <div>
        <label>A. Riwayat Perkawinan Penjamin</label>
        <textarea name="penjamin_perkawinan" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>B. Relasi Sosial dalam Keluarga</label>
        <textarea name="penjamin_relasi_keluarga" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>C. Relasi Sosial dengan Masyarakat</label>
        <textarea name="penjamin_relasi_masyarakat" class="input w-full h-20"></textarea>
    </div>

    <h4 class="font-semibold mb-3">
        D. Pekerjaan dan Keadaan Ekonomi
    </h4>

    <div>
        <label>1. Pekerjaan</label>
        <textarea name="penjamin_pekerjaan" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>2. Keadaan Rumah Tempat Tinggal Penjamin</label>
        <textarea name="penjamin_rumah" class="input w-full h-20"></textarea>
    </div>

</div>


{{-- ================= III ================= --}}
<h3 class="text-lg font-semibold mt-8 mb-4">
    III. KONDISI LINGKUNGAN SOSIAL, BUDAYA TEMPAT TINGGAL KLIEN
</h3>

<div class="space-y-4 mb-6">

    <div>
        <label>A. Relasi Sosial Antar Anggota Masyarakat</label>
        <textarea name="lingkungan_relasi" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>B. Kondisi Ekonomi, Budaya, Pendidikan dan Lingkungan</label>
        <textarea name="lingkungan_kondisi" class="input w-full h-20"></textarea>
    </div>

    <h4 class="font-semibold mb-3">
            C. Keadaan Masyarakat
    </h4>

    <div>
        <label>1. Penggolongan Profesi dan Mata Pencaharian</label>
        <textarea name="lingkungan_profesi" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>2. Stratifikasi Sosial Ekonomi</label>
        <textarea name="lingkungan_strata" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>3. Tingkat Pendidikan Rata-rata</label>
        <textarea name="lingkungan_pendidikan" class="input w-full h-20"></textarea>
    </div>

    <h4 class="font-semibold mb-3">
        D. Pola Hubungan (Interaksi Sosial) Dalam Masyarakat
    </h4>

    <div>
        <label>1. Kepedulian terhadap Kehidupan Masyarakat</label>
        <textarea name="kepedulian_masyarakat" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>2. Kepedulian terhadap Pendidikan</label>
        <textarea name="kepedulian_pendidikan" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>3. Kepedulian terhadap Kegiatan Keagamaan</label>
        <textarea name="kepedulian_agama" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>4. Kepedulian terhadap Penegak Hukum</label>
        <textarea name="kepedulian_hukum" class="input w-full h-20"></textarea>
    </div>

</div>


{{-- ================= IV ================= --}}
<h3 class="text-lg font-semibold mt-8 mb-4">
    IV. RIWAYAT TINDAK PIDANA
</h3>

<div class="space-y-4 mb-6">

    <div>
        <label>A. Latar Belakang</label>
        <textarea name="pidana_latar" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>B. Kronologis</label>
        <textarea name="pidana_kronologis" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>C. Keadaan Korban</label>
        <textarea name="pidana_korban" class="input w-full h-20"></textarea>
    </div>

    <h4 class="font-semibold mb-3">
        D. Akibat Yang Ditimbulkan Terhadap Klien, Keluarga dan Masyarakat
    </h4>

    <div>
        <label>1. Akibat bagi Klien</label>
        <textarea name="akibat_klien" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>2. Akibat bagi Keluarga</label>
        <textarea name="akibat_keluarga" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>3. Akibat bagi Masyarakat</label>
        <textarea name="akibat_masyarakat" class="input w-full h-20"></textarea>
    </div>

</div>


{{-- ================= V ================= --}}
<h3 class="text-lg font-semibold mt-8 mb-4">
    V. TANGGAPAN KLIEN, KELUARGA, MASYARAKAT DAN PEMERINTAH
</h3>

<div class="space-y-4 mb-6">

    <div>
        <label>A. Tanggapan Klien</label>
        <textarea name="tanggapan_klien" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>B. Tanggapan Keluarga</label>
        <textarea name="tanggapan_keluarga" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>C. Tanggapan Masyarakat</label>
        <textarea name="tanggapan_masyarakat" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>D. Tanggapan Pemerintah Setempat</label>
        <textarea name="tanggapan_pemerintah" class="input w-full h-20"></textarea>
    </div>

    
{{-- ================= VI ================= --}}
<h3 class="text-lg font-semibold mt-8 mb-4">
    VI. EVALUASI PERKEMBANGAN PEMBINAAN KLIEN
</h3>

<div class="space-y-4 mb-6">

    <div>
        <label>A. Evaluasi Program Admisi, Orientasi dan Observasi</label>
        <textarea name="evaluasi_admisi" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label class="font-semibold">Tanggal Tahapan Pembinaan (SDP)</label>

        <div class="grid grid-cols-3 gap-4 mt-2">
            <div>
                <label>1/3 Masa Pidana</label>
                <input type="date" name="tgl_sepertiga" class="input">
            </div>

            <div>
                <label>1/2 Masa Pidana</label>
                <input type="date" name="tgl_setengah" class="input">
            </div>

            <div>
                <label>2/3 Masa Pidana</label>
                <input type="date" name="tgl_duapertiga" class="input">
            </div>
        </div>
    </div>

    <div>
        <label>B. Program Pembinaan Kepribadian</label>
        <textarea name="pembinaan_kepribadian" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>C. Program Pembinaan Kemandirian</label>
        <textarea name="pembinaan_kemandirian" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label class="font-semibold">D. Relasi Sosial Selama di Rutan</label>

        <div>
            <label>1. Sesama WBP</label>
            <textarea name="relasi_wbp" class="input w-full h-20"></textarea>
        </div>
        <div>
            <label>2. Petugas</label>
            <textarea name="relasi_petugas" class="input w-full h-20"></textarea>
        </div>
        <div>
            <label>3. Keluarga</label>
            <textarea name="relasi_keluarga" class="input w-full h-20"></textarea>
        </div>
        <div>
            <label>4. Masyarakat</label>
            <textarea name="relasi_masyarakat" class="input w-full h-20"></textarea>
        </div>
    </div>

</div>


{{-- ================= VII ================= --}}
<h3 class="text-lg font-semibold mt-8 mb-4">
    VII. HASIL / REKOMENDASI ASESMEN
</h3>

<div class="mb-6">
    <textarea name="hasil_asesmen" class="input w-full h-24"></textarea>
</div>


{{-- ================= VIII ================= --}}
<h3 class="text-lg font-semibold mt-8 mb-4">
    VIII. ANALISIS
</h3>

<div class="space-y-4 mb-6">

    <div>
        <label>A. Sikap Klien & Risiko Pengulangan</label>
        <textarea name="analisis_resiko" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>B. Hasil Program Pembinaan</label>
        <textarea name="analisis_hasil" class="input w-full h-20"></textarea>
    </div>

    <div>
        <label>C. Penerimaan Masyarakat, Pemerintah dan Korban</label>
        <textarea name="analisis_penerimaan" class="input w-full h-20"></textarea>
    </div>

</div>


{{-- ================= IX ================= --}}
<h3 class="text-lg font-semibold mt-8 mb-4">
    IX. KESIMPULAN DAN REKOMENDASI
</h3>

<div class="space-y-4 mb-6">

    <div>
        <label>A. Kesimpulan</label>
        <textarea name="kesimpulan" class="input w-full h-24"></textarea>
    </div>

    <div>
                <label>Tanggal Rekomendasi</label>
                <input type="date" name="tgl_rekomendasi" class="input">
            </div>

    <div>
        <label>B. Rekomendasi</label>
        <textarea name="rekomendasi" class="input w-full h-24"></textarea>
    </div>

</div>
</div>

    {{-- BUTTON --}}
    <div class="flex justify-between mt-6">
        <button type="button" onclick="prevStep(2)" class="btn-gray">
            ← Back
        </button>

        <button type="submit" class="btn-green">
            Simpan & Preview
        </button>
    </div>

</div>

    </form>
</div>

{{-- STYLE --}}
<style>
.input { width:100%; border:1px solid #ccc; padding:8px; border-radius:6px; }
.btn-blue { background:#2563eb; color:white; padding:8px 16px; border-radius:6px; }
.btn-gray { background:#6b7280; color:white; padding:8px 16px; border-radius:6px; }
.btn-green { background:#16a34a; color:white; padding:8px 16px; border-radius:6px; }

.input-modern{
    width: 100%;
    border: 1px solid #d1d5db;
    padding: 4px 6px; /* diperkecil */
    font-size: 12px; /* lebih kecil */
    border-radius: 6px;
}

textarea.input {
    resize: vertical;
    line-height: 1.4;
}
</style>

{{-- =========================
    JAVASCRIPT
========================= --}}
<script>

/* STEP */
function nextStep(step){
    document.getElementById('step-1').style.display='none';
    document.getElementById('step-2').style.display='none';
    document.getElementById('step-3').style.display='none';
    document.getElementById('step-'+step).style.display='block';

    // ⬇️ SCROLL KE ATAS
    window.scrollTo({
        top: 0,
        behavior: 'auto' // bisa diganti 'auto' kalau mau langsung
    });
}

function prevStep(step){
    nextStep(step);
}

function handleNext(){
    document.getElementById('client_id').dispatchEvent(new Event('change'));
    nextStep(2);
}

/* FORMAT TANGGAL */
function formatTanggal(tanggal){
    if(!tanggal) return '';

    const bulan = ["Januari","Februari","Maret","April","Mei","Juni",
                   "Juli","Agustus","September","Oktober","November","Desember"];

    let d = new Date(tanggal);
    return d.getDate()+' '+bulan[d.getMonth()]+' '+d.getFullYear();
}

/* =========================
   TAMBAH DASAR HUKUM
========================= */

// TEMPLATE OPTION (biar tidak nulis ulang blade di JS)
let pasalOptions = `
<option value="">-- Pilih Pasal --</option>
@foreach($pasals as $pasal)
<option value="{{ $pasal->id }}">
    Pasal {{ $pasal->nomor_pasal }} -
    {{ $pasal->klasifikasiHukum->nama_klasifikasi ?? '-' }}
</option>
@endforeach
`;

/* TAMBAH */
function tambahDasarHukum(){

    let html = `
    <div class="flex gap-2">
        <select name="pasal_id[]" class="input">
            ${pasalOptions}
        </select>

        <button type="button"
            onclick="hapusDasarHukum(this)"
            class="bg-red-500 text-white px-3 rounded">
            🗑
        </button>
    </div>
    `;

    document.getElementById('dasar_hukum_wrapper')
        .insertAdjacentHTML('beforeend', html);
}

/* HAPUS */
function hapusDasarHukum(btn){

    let wrapper = document.getElementById('dasar_hukum_wrapper');

    // minimal 1 tetap ada
    if(wrapper.children.length > 1){
        btn.parentElement.remove();
    } else {
        alert('Minimal 1 dasar hukum harus ada');
    }
}

/* =========================
   AUTO FILL CLIENT
========================= */
document.getElementById('client_id').addEventListener('change', function(){

    let opt = this.options[this.selectedIndex];

    let ttl = opt.dataset.tempat + ', ' + formatTanggal(opt.dataset.tanggal);

    // STEP 1
    document.getElementById('ttl_1').value = ttl;
    document.getElementById('alamat_1').value = opt.dataset.alamat ?? '';

    // STEP 2
    document.getElementById('nama_klien').value = opt.dataset.nama ?? '';
    document.getElementById('ttl_2').value = ttl;

    document.getElementById('jenis_kelamin').value =
        opt.dataset.jk == 'L' ? 'Laki-laki' :
        opt.dataset.jk == 'P' ? 'Perempuan' : '';

    document.getElementById('status').value = opt.dataset.status ?? '';
    document.getElementById('agama').value = opt.dataset.agama ?? '';
    document.getElementById('pendidikan').value = opt.dataset.pendidikan ?? '';
    document.getElementById('pekerjaan').value = opt.dataset.pekerjaan ?? '';
    document.getElementById('alamat_2').value = opt.dataset.alamat ?? '';
    document.getElementById('ciri').value = opt.dataset.ciri ?? '';

    // NAMA PK DARI CLIENT
    document.getElementById('nama_pk').value =
        opt.dataset.user ?? 'Tidak ada PK';


    /* =========================
       AMBIL DATA ORTU / PENJAMIN
    ========================== */
    let clientId = this.value;

fetch(`/ajax/keluarga/${clientId}`, {
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    credentials: 'same-origin'
})
.then(res => res.json())
.then(data => {

    console.log('DATA KELUARGA:', data);

    data.forEach(d => {
        console.log('JENIS:', d.jenis);
    });

    let ayah = data.filter(d => d.jenis === 'ayah');
    let ibu = data.filter(d => d.jenis === 'ibu');
    
    // PENJAMIN = SEMUA DATA
    let penjamin = data;

    console.log('AYAH:', ayah);
    console.log('IBU:', ibu);
    console.log('PENJAMIN:', penjamin);

    setDropdown('ayah_select', ayah);
    setDropdown('ibu_select', ibu);
    setDropdown('penjamin_select', penjamin);

})
.catch(err => console.error('Error keluarga:', err));

});


/* =========================
   SET DROPDOWN
========================= */
function setDropdown(id, data){
    let el = document.getElementById(id);

    if(!el) return; // biar aman kalau elemen belum ada

    el.innerHTML = '<option value="">-- Pilih --</option>';

    data.forEach(item => {
        let opt = document.createElement('option');
        opt.value = item.id;
        opt.text = item.nama;
        opt.dataset.data = JSON.stringify(item);
        el.appendChild(opt);
    });
}


/* =========================
   ISI DETAIL DATA
========================= */
function isiData(prefix, data){

    let ttl = data.tempat_lahir + ', ' + formatTanggal(data.tanggal_lahir);

    setVal(prefix+'_ttl', ttl);
    setVal(prefix+'_agama', data.agama);
    setVal(prefix+'_suku', data.suku);
    setVal(prefix+'_warga', data.kewarganegaraan);
    setVal(prefix+'_pendidikan', data.pendidikan_terakhir);
    setVal(prefix+'_pekerjaan', data.pekerjaan);
    setVal(prefix+'_alamat', data.alamat);
    setVal(prefix+'_hubungan', data.hubungan_keluarga);
}


/* =========================
   HELPER BIAR AMAN
========================= */
function setVal(id, value){
    let el = document.getElementById(id);
    if(el) el.value = value ?? '';
}


/* =========================
   EVENT DROPDOWN ORTU
========================= */
['ayah','ibu','penjamin'].forEach(prefix => {

    let el = document.getElementById(prefix+'_select');

    if(!el) return;

    el.addEventListener('change', function(){

        let data = this.options[this.selectedIndex].dataset.data;

        if (data) {
            isiData(prefix, JSON.parse(data));
        } else {
            isiData(prefix, {}); // reset kalau kosong
        }

    });

});


// MENAMBAH BARI TABEL

function tambahBaris(){

    let row = `
    <tr class="hover:bg-gray-50 transition">

        <td class="p-2">
            <input type="text" name="keluarga[nama][]"
                class="input-modern">
        </td>

        <td class="p-2">
            <select name="keluarga[jk][]" class="input-modern">
                <option value="L">L</option>
                <option value="P">P</option>
            </select>
        </td>

        <td class="p-2">
            <input type="number" name="keluarga[usia][]"
                class="input-modern">
        </td>

        <td class="p-2">
            <input type="text" name="keluarga[pendidikan][]"
                class="input-modern">
        </td>

        <td class="p-2">
            <input type="text" name="keluarga[pekerjaan][]"
                class="input-modern">
        </td>

        <td class="p-2">
            <input type="text" name="keluarga[ket][]"
                class="input-modern">
        </td>

        <td class="p-2 text-center">
            <button type="button"
            onclick="hapusBaris(this)"
            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm">
            🗑
        </button>
        </td>

    </tr>
    `;

    document.getElementById('table_keluarga')
        .insertAdjacentHTML('beforeend', row);
}

function hapusBaris(btn){
    btn.closest('tr').remove();
}

</script>

@endsection 