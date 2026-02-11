@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Data Klien</h1>

    <a href="{{ route('clients.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Tambah Klien
    </a>
</div>

{{-- FLASH MESSAGE --}}
@if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-100 text-green-700">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
        {{ session('error') }}
    </div>
@endif

{{-- FILTER --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">

    {{-- SEARCH --}}
    <form method="GET">
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Cari nama klien..."
               class="px-3 py-2 border rounded-lg w-64">
    </form>

    {{-- PER PAGE --}}
    <form method="GET">
        <input type="hidden" name="search" value="{{ request('search') }}">

        <select name="per_page"
                onchange="this.form.submit()"
                class="px-3 py-2 pr-8 border rounded-lg">
            @foreach([10,15,20,30,50] as $size)
                <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                    {{ $size }}
                </option>
            @endforeach
        </select>
    </form>

</div>

{{-- TABLE --}}
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full border-collapse text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-3 py-2 text-left">No</th>
                <th class="px-3 py-2 text-left">Nama</th>
                <th class="px-3 py-2 text-left">No Register</th>
                <th class="px-3 py-2 text-left">Tempat Lahir</th>
                <th class="px-3 py-2 text-left">Tanggal Lahir</th>
                <th class="px-3 py-2 text-left">Jenis Kelamin</th>
                <th class="px-3 py-2 text-left">Agama</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-left">Suku</th>
                <th class="px-3 py-2 text-left">Kebangsaan</th>
                <th class="px-3 py-2 text-left">Kewarganegaraan</th>
                <th class="px-3 py-2 text-left">Pendidikan</th>
                <th class="px-3 py-2 text-left">Pekerjaan</th>
                <th class="px-3 py-2 text-left">Alamat</th>
                <th class="px-3 py-2 text-left">Ciri Khusus</th>
                <th class="px-3 py-2 text-left">Usia</th>
                <th class="px-3 py-2 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($clients as $client)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2 font-semibold">{{ $client->nama }}</td>
                    <td class="px-3 py-2">{{ $client->no_register }}</td>
                    <td class="px-3 py-2">{{ $client->tempat_lahir ?? '-' }}</td>
                    <td class="px-3 py-2">
                        {{ $client->tanggal_lahir ? \Carbon\Carbon::parse($client->tanggal_lahir)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="px-3 py-2">{{ $client->jenis_kelamin }}</td>
                    <td class="px-3 py-2">{{ $client->agama ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $client->status_perkawinan ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $client->suku ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $client->kebangsaan ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $client->kewarganegaraan ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $client->pendidikan ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $client->pekerjaan ?? '-' }}</td>
                    <td class="px-3 py-2">
                        {{ \Illuminate\Support\Str::limit($client->alamat, 40) ?? '-' }}
                    </td>
                    <td class="px-3 py-2">
                        {{ \Illuminate\Support\Str::limit($client->ciri_khusus, 40) ?? '-' }}
                    </td>
                    <td class="px-3 py-2">{{ $client->usia ?? '-' }}</td>

                    {{-- AKSI --}}
                    <td class="px-3 py-2 flex gap-2">
                        <a href="{{ route('clients.show', $client) }}"
                           class="text-blue-600 hover:underline">
                            Detail
                        </a>

                        <a href="{{ route('clients.edit', $client) }}"
                           class="text-green-600 hover:underline">
                            Edit
                        </a>

                        <form method="POST"
                              action="{{ route('clients.destroy', $client) }}"
                              onsubmit="return confirm('Hapus data klien ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:underline">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="17"
                        class="px-4 py-6 text-center text-gray-500">
                        Data klien belum tersedia
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- PAGINATION --}}
<div class="mt-4">
    {{ $clients->links() }}
</div>

@endsection
