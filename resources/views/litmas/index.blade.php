@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- =========================
        MODE 1: PILIH JENIS
    ========================== --}}
    @if(!$jenis)

        <h1 class="text-2xl font-semibold mb-2">
            Buat Litmas Baru
        </h1>

        <p class="text-gray-600 mb-6">
            Pilih jenis Litmas yang akan dibuat
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <a href="{{ route('litmas.index', ['jenis' => 'anak']) }}"
               class="block p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h2 class="text-lg font-semibold">Litmas Anak</h2>
                <p class="text-sm text-gray-500 mt-1">Anak &lt; 12 Tahun, Diversi, Sidang Anak</p>
            </a>

            <a href="{{ route('litmas.index', ['jenis' => 'dewasa']) }}"
               class="block p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h2 class="text-lg font-semibold">Litmas Dewasa</h2>
                <p class="text-sm text-gray-500 mt-1">Tersangka &amp; Tahanan Dewasa</p>
            </a>

            <a href="{{ route('litmas.index', ['jenis' => 'awal']) }}"
               class="block p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h2 class="text-lg font-semibold">Litmas Awal/Pembinaan</h2>
                <p class="text-sm text-gray-500 mt-1">Pembinaan &amp; Rehabilitasi</p>
            </a>

        </div>

    {{-- =========================
        MODE 2: PILIH KATEGORI
    ========================== --}}
    @else

        <h1 class="text-2xl font-semibold mb-2 capitalize">
            Litmas {{ str_replace('_', ' ', $jenis) }}
        </h1>

        <p class="text-gray-600 mb-6">Pilih jenis layanan</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

            <a href="{{ route('litmas.form', ['jenis_litmas' => $jenis, 'kategori' => 'pembebasan_bersyarat']) }}"
               class="block p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h2 class="text-lg font-semibold">Pembebasan Bersyarat</h2>
                <p class="text-sm text-gray-500 mt-1">Proses pembebasan sebelum masa pidana selesai</p>
            </a>

            <a href="{{ route('litmas.form', ['jenis_litmas' => $jenis, 'kategori' => 'cuti_bersyarat']) }}"
               class="block p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h2 class="text-lg font-semibold">Cuti Bersyarat</h2>
                <p class="text-sm text-gray-500 mt-1">Cuti dengan syarat tertentu</p>
            </a>

        </div>

    @endif

    {{-- =========================
        TABEL DATA LITMAS TERSIMPAN
        (tampil di semua mode)
    ========================== --}}
    <div class="mt-10">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Data Litmas Tersimpan</h2>
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

        {{-- SEARCH + PER PAGE --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">

            {{-- SEARCH --}}
            <form method="GET" action="{{ route('litmas.index') }}" id="searchForm">
                @if($jenis)
                    <input type="hidden" name="jenis" value="{{ $jenis }}">
                @endif
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="relative">
                    <input
                        type="text"
                        id="searchInput"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama klien atau perkara..."
                        class="h-11 rounded-lg border border-blue-500 px-4 pr-10 text-sm focus:outline-none w-72"
                        autocomplete="off"
                    >
                    <button type="button" id="clearBtn"
                            class="hidden absolute right-3 top-1/2 -translate-y-1/2
                                   h-5 w-5 flex items-center justify-center rounded-full
                                   text-gray-400 hover:bg-gray-200 hover:text-red-500 text-xs">
                        ✕
                    </button>
                </div>
            </form>

            {{-- PER PAGE --}}
            <form method="GET" action="{{ route('litmas.index') }}">
                @if($jenis)
                    <input type="hidden" name="jenis" value="{{ $jenis }}">
                @endif
                <input type="hidden" name="search" value="{{ request('search') }}">
                <select name="per_page" onchange="this.form.submit()"
                        class="px-3 py-2 border rounded-lg text-sm">
                    @foreach([10, 15, 20, 30, 50] as $size)
                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                            {{ $size }} / halaman
                        </option>
                    @endforeach
                </select>
            </form>

        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">No</th>
                        <th class="px-4 py-3 text-left">Nama Klien</th>
                        <th class="px-4 py-3 text-left">Penjamin</th>
                        <th class="px-4 py-3 text-left">Perkara</th>
                        <th class="px-4 py-3 text-center w-56">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($litmasList as $litmas)
                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3 text-gray-500">
                                {{ ($litmasList->currentPage() - 1) * $litmasList->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $litmas->client->nama ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $litmas->guarantor->nama ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $litmas->perkara ? \Illuminate\Support\Str::limit($litmas->perkara, 60) : '-' }}
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3">
                                <div class="flex justify-center items-center gap-2 flex-wrap">

                                    {{-- PREVIEW --}}
                                    <a href="{{ route('export.preview', $litmas->id) }}"
                                       class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                        👁 Preview
                                    </a>

                                    {{-- DOWNLOAD WORD --}}
                                    <a href="{{ route('export.word', $litmas->id) }}"
                                       class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition">
                                        ⬇ Word
                                    </a>

                                    {{-- EDIT --}}
                                    {{-- <a href="{{ route('litmas.edit', $litmas->id) }}"
                                       class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200 transition">
                                        ✏ Edit
                                    </a> --}}

                                    {{-- HAPUS --}}
                                    {{-- <form method="POST"
                                          action="{{ route('litmas.destroy', $litmas->id) }}"
                                          onsubmit="return confirm('Hapus data litmas {{ $litmas->client->nama ?? '' }} ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                            🗑 Hapus
                                        </button>
                                    </form> --}}

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                Belum ada data litmas tersimpan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION — appends() agar search & per_page tidak hilang --}}
        <div class="mt-4">
            {{ $litmasList->appends(request()->query())->links() }}
        </div>

    </div>

    {{-- BACK — hanya tampil jika sedang di mode pilih kategori --}}
    @if($jenis)
        <a href="{{ route('litmas.index') }}"
           class="fixed bottom-6 left-10 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            ← Kembali
        </a>
    @endif

</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearBtn');
    const searchForm  = document.getElementById('searchForm');
    let   debounceTimer;

    function toggleClearButton() {
        clearBtn.classList.toggle('hidden', searchInput.value.length === 0);
    }

    function clearSearch() {
        clearTimeout(debounceTimer);
        searchInput.value = '';
        toggleClearButton();
        searchForm.submit();
    }

    document.addEventListener('DOMContentLoaded', toggleClearButton);

    searchInput.addEventListener('input', function () {
        toggleClearButton();
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => searchForm.submit(), 500);
    });

    clearBtn.addEventListener('click', clearSearch);
</script>

@endsection