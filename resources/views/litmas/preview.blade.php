@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">
        Preview Litmas
    </h2>

    {{-- =========================
        PREVIEW DATA
    ========================= --}}
    <div class="border p-4 rounded mb-6">

        <p><b>Nama Klien:</b> {{ $litmas->client->name ?? '-' }}</p>
        <p><b>Nama Petugas:</b> {{ $litmas->user->name ?? '-' }}</p>
        <p><b>Perkara:</b> {{ $perkara }}</p>
        <p><b>Penjamin:</b> {{ $litmas->guarantor->name ?? '-' }}</p>

        <hr class="my-4">

        <h3 class="font-semibold mb-2">Susunan Keluarga</h3>

        <table class="w-full border text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Nama</th>
                    <th class="p-2 border">L/P</th>
                    <th class="p-2 border">Usia</th>
                    <th class="p-2 border">Pendidikan</th>
                    <th class="p-2 border">Pekerjaan</th>
                    <th class="p-2 border">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($litmas->families as $f)
                <tr>
                    <td class="border p-2">{{ $f->nama }}</td>
                    <td class="border p-2">{{ $f->jk }}</td>
                    <td class="border p-2">{{ $f->usia }}</td>
                    <td class="border p-2">{{ $f->pendidikan }}</td>
                    <td class="border p-2">{{ $f->pekerjaan }}</td>
                    <td class="border p-2">{{ $f->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    {{-- =========================
        BUTTON ACTION
    ========================= --}}
    <div class="flex justify-between">

        {{-- BACK --}}
        <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded">
            ← Kembali ke Form
        </a>

        <div class="flex gap-2">

            {{-- DOWNLOAD WORD --}}
            <a href="{{ route('export.word', $litmas->id) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                Download Word
            </a>

            {{-- DOWNLOAD PDF --}}
            <a href="{{ route('export.pdf', $litmas->id) }}"
               class="bg-green-600 text-white px-4 py-2 rounded">
                Download PDF
            </a>

        </div>

    </div>

</div>

@endsection