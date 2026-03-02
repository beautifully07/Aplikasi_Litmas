@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Detail Penjamin</h1>
</div>

{{-- CARD --}}
<div class="bg-white rounded-lg shadow p-6">

    {{-- DATA KLIEN --}}
    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
        Data Klien
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Nama Klien</p>
            <p class="font-semibold">
                {{ $penjamin->client->nama ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">No Register</p>
            <p class="font-semibold">
                {{ $penjamin->client->no_register ?? '-' }}
            </p>
        </div>
    </div>

    {{-- DATA PENJAMIN --}}
    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
        Data Penjamin
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <p class="text-sm text-gray-500">Nama</p>
            <p class="font-semibold">
                {{ $penjamin->nama ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Hubungan Keluarga</p>
            <p class="font-semibold">
                {{ $penjamin->hubungan_keluarga ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">No KK</p>
            <p class="font-semibold">
                {{ $penjamin->no_kk ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Tempat, Tanggal Lahir</p>
            <p class="font-semibold">
                {{ $penjamin->tempat_lahir ?? '-' }},
                {{ $penjamin->tanggal_lahir
                    ? \Carbon\Carbon::parse($penjamin->tanggal_lahir)->format('d-m-Y')
                    : '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Usia</p>
            <p class="font-semibold">
                {{ $penjamin->usia ?? '-' }} Tahun
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Agama</p>
            <p class="font-semibold">
                {{ $penjamin->agama ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Suku</p>
            <p class="font-semibold">
                {{ $penjamin->suku ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Kewarganegaraan</p>
            <p class="font-semibold">
                {{ $penjamin->kewarganegaraan ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Pendidikan Terakhir</p>
            <p class="font-semibold">
                {{ $penjamin->pendidikan_terakhir ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Pekerjaan</p>
            <p class="font-semibold">
                {{ $penjamin->pekerjaan ?? '-' }}
            </p>
        </div>

        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Alamat</p>
            <p class="font-semibold">
                {{ $penjamin->alamat ?? '-' }}
            </p>
        </div>

    </div>

    {{-- AKSI --}}
    <div class="mt-8 flex gap-3">

        <a href="{{ route('penjamin.edit', $penjamin) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit
        </a>

        <form action="{{ route('penjamin.destroy', $penjamin) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus data penjamin ini?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Hapus
            </button>
        </form>

        <a href="{{ route('penjamin.index') }}"
           class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            ← Kembali
        </a>

    </div>

</div>

@endsection