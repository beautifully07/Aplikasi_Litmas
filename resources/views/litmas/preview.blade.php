@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">
        Preview Litmas
    </h2>

    <div class="border p-4 rounded mb-6">
        <p><b>Nama Klien:</b> {{ $litmas->client->nama ?? '-' }}</p>
        <p><b>Nama Petugas:</b> {{ $litmas->user->name ?? '-' }}</p>
        <p><b>Perkara:</b> {{ $perkara }}</p>
        <p><b>Penjamin:</b> {{ $litmas->guarantor->nama ?? '-' }}</p>
    </div>

    <div class="flex justify-between">

        {{-- BACK --}}
        <a href="{{ route('litmas.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
            ← Kembali ke Daftar
        </a>

        <div class="flex gap-2">

            {{-- DOWNLOAD WORD --}}
            <a href="{{ route('export.word', $litmas->id) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                Simpan dan Download Word
            </a>

        </div>

    </div>

</div>

@endsection