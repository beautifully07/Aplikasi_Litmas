@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-800">
            Edit Data Penjamin
        </h1>

        <a href="{{ route('penjamin.index') }}"
           class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
            Kembali
        </a>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="{{ route('penjamin.update', $penjamin->id) }}">
        @csrf
        @method('PUT')

        {{-- PILIH CLIENT --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Klien</label>
            <select name="client_id"
                    class="w-full border rounded px-3 py-2">
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}"
                        {{ old('client_id', $penjamin->client_id) == $client->id ? 'selected' : '' }}>
                        {{ $client->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- NAMA --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Nama Penjamin</label>
            <input type="text"
                   name="nama"
                   value="{{ old('nama', $penjamin->nama) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
        </div>

        {{-- NO KK --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">No KK</label>
            <input type="number"
                   name="no_kk"
                   value="{{ old('no_kk', $penjamin->no_kk) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- TEMPAT & TANGGAL LAHIR --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-1">Tempat Lahir</label>
                <input type="text"
                       name="tempat_lahir"
                       value="{{ old('tempat_lahir', $penjamin->tempat_lahir) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold mb-1">Tanggal Lahir</label>
                <input type="date"
                       name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', $penjamin->tanggal_lahir) }}"
                       class="w-full border rounded px-3 py-2"
                       required>
            </div>
        </div>

        {{-- HUBUNGAN --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Hubungan Keluarga</label>
            <select name="hubungan_keluarga"
                    class="w-full border rounded px-3 py-2"
                    required>
                @foreach (['Ayah Kandung','Ibu Kandung','Saudara Kandung','Suami','Istri'] as $hub)
                    <option value="{{ $hub }}"
                        {{ old('hubungan_keluarga', $penjamin->hubungan_keluarga) == $hub ? 'selected' : '' }}>
                        {{ $hub }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- AGAMA --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Agama</label>
            <input type="text"
                   name="agama"
                   value="{{ old('agama', $penjamin->agama) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- SUKU --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Suku</label>
            <input type="text"
                   name="suku"
                   value="{{ old('suku', $penjamin->suku) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- KEWARGANEGARAAN --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Kewarganegaraan</label>
            <input type="text"
                   name="kewarganegaraan"
                   value="{{ old('kewarganegaraan', $penjamin->kewarganegaraan) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- PENDIDIKAN TERAKHIR --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Pendidikan Terakhir</label>
            <input type="text"
                name="pendidikan_terakhir"
                value="{{ old('pendidikan_terakhir', $penjamin->pendidikan_terakhir) }}"
                class="w-full border rounded px-3 py-2"
                required>
        </div>

        {{-- PEKERJAAN --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Pekerjaan</label>
            <input type="text"
                   name="pekerjaan"
                   value="{{ old('pekerjaan', $penjamin->pekerjaan) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- ALAMAT --}}
        <div class="mb-6">
            <label class="block font-semibold mb-1">Alamat</label>
            <textarea name="alamat"
                      rows="3"
                      class="w-full border rounded px-3 py-2">{{ old('alamat', $penjamin->alamat) }}</textarea>
        </div>

        {{-- BUTTON --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('penjamin.index') }}"
               class="px-4 py-2 border rounded hover:bg-gray-100">
                Batal
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection