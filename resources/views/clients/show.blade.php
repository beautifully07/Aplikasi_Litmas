@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Detail Klien
    </h1>

    <a href="{{ route('clients.index') }}"
       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
        ← Kembali
    </a>
</div>

{{-- CARD --}}
<div class="bg-white rounded-lg shadow p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- DATA UTAMA --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 text-gray-700">
                Data Pribadi
            </h2>

            <table class="w-full text-sm">
                <tr>
                    <td class="py-2 text-gray-500 w-40">Nama</td>
                    <td class="py-2 font-semibold">{{ $client->nama }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">No Register</td>
                    <td class="py-2">{{ $client->no_register }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500 w-40">Tempat Lahir</td>
                    <td class="py-2">{{ $client->tempat_lahir ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Tanggal Lahir</td>
                    <td class="py-2">
                        {{ $client->tanggal_lahir
                            ? \Carbon\Carbon::parse($client->tanggal_lahir)->format('d-m-Y')
                            : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Jenis Kelamin</td>
                    <td class="py-2">{{ $client->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Status</td>
                    <td class="py-2">{{ $client->status_perkawinan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Pekerjaan</td>
                    <td class="py-2">{{ $client->pekerjaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Usia</td>
                    <td class="py-2">{{ $client->usia ?? '-' }} tahun</td>
                </tr>
            </table>
        </div>

        {{-- DATA TAMBAHAN --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 text-gray-700">
                Informasi Tambahan
            </h2>

            <table class="w-full text-sm">
                <tr>
                    <td class="py-2 text-gray-500">Pendidikan</td>
                    <td class="py-2">{{ $client->pendidikan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Alamat</td>
                    <td class="py-2">{{ $client->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Kebangsaan</td>
                    <td class="py-2">{{ $client->kebangsaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Ciri Khusus</td>
                    <td class="py-2">{{ $client->ciri_khusus ?? '-' }}</td>
                </tr>
            </table>
        </div>

    </div>

    {{-- AKSI --}}
    <div class="flex gap-3 mt-8">
        <a href="{{ route('clients.edit', $client) }}"
           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Edit
        </a>

        <form method="POST"
              action="{{ route('clients.destroy', $client) }}"
              onsubmit="return confirm('Yakin ingin menghapus data klien ini?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Hapus
            </button>
        </form>
    </div>
</div>

@endsection
