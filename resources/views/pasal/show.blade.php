@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Detail Dasar Hukum
    </h1>
</div>

{{-- CARD --}}
<div class="bg-white rounded-lg shadow p-6">

    {{-- DATA PASAL --}}
    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
        Detail {{ $pasal->judul }}
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <div>
            <p class="text-sm text-gray-500">Klasifikasi Hukum</p>
            <p class="font-semibold">
                {{ $pasal->judul ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Nomor Pasal</p>
            <p class="font-semibold">
                {{ $pasal->nomor_pasal ?? '-' }}
            </p>
        </div>
    </div>

    {{-- DATA AYAT --}}
    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
        Daftar Ayat
    </h2>

    @forelse($pasal->ayats as $ayat)
        <div class="border rounded-lg p-4 mb-4 bg-gray-50">
            <p class="font-semibold mb-2">
                Ayat ({{ $ayat->nomor_ayat }})
            </p>
            <p class="text-gray-700 leading-relaxed">
                {{ $ayat->isi }}
            </p>
        </div>
    @empty
        <p class="text-gray-500">Tidak ada ayat.</p>
    @endforelse


    {{-- AKSI --}}
    <div class="mt-8 flex gap-3">

        <a href="{{ route('pasal.edit', $pasal) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit
        </a>

        <form action="{{ route('pasal.destroy', $pasal) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus pasal ini?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Hapus
            </button>
        </form>

        <a href="{{ route('pasal.index') }}"
           class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            ← Kembali
        </a>

    </div>

</div>

@endsection