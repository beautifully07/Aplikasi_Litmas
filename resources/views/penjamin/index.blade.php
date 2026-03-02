@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow">

    {{-- HEADER --}}
    <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
        <h1 class="text-2xl font-bold">Data Penjamin</h1>

        <a href="{{ route('penjamin.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Tambah Penjamin
        </a>
    </div>
{{-- SEARCH --}}
<form method="GET" action="{{ route('penjamin.index') }}" class="mb-4">
    <div style="position: relative;">
        <input
            type="text"
            id="searchInput"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari penjamin / klien..."
            class="w-full h-11 rounded-lg
                   border border-blue-500
                   px-4 pr-12 text-sm
                   focus:outline-none"
            oninput="toggleClearButton()"
        >

        <!-- CLEAR BUTTON -->
        <button
            type="button"
            id="clearBtn"
            onclick="clearSearch()"
            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);"
            class="hidden
                   h-6 w-6
                   flex items-center justify-center
                   rounded-full
                   text-gray-400
                   hover:bg-gray-200 hover:text-red-500"
        >
            ✕
        </button>
    </div>
</form>

    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border text-center">No</th>
                    <th class="px-3 py-2 border">Nama Penjamin</th>
                    <th class="px-3 py-2 border">Klien</th>
                    <th class="px-3 py-2 border">Hubungan</th>
                    <th class="px-3 py-2 border text-center">Usia</th>
                    <th class="px-3 py-2 border text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjamins as $p)
                <tr>
                    <td class="px-3 py-2 border text-center">
                        {{ $loop->iteration + ($penjamins->currentPage() - 1) * $penjamins->perPage() }}
                    </td>
                    <td class="px-3 py-2 border text-center">{{ $p->nama }}</td>
                    <td class="px-3 py-2 border text-center">{{ $p->client->nama ?? '-' }}</td>
                    <td class="px-3 py-2 border text-center">{{ $p->hubungan_keluarga }}</td>
                    <td class="px-3 py-2 border text-center">{{ $p->usia }}</td>

                    {{-- AKSI --}}
                    <td class="px-3 py-2 border text-center">
                        <div class="flex justify-center gap-2">

                            <a href="{{ route('penjamin.show', $p->id) }}"
                               class="text-blue-600 hover:underline">
                                Detail
                            </a>

                            <a href="{{ route('penjamin.edit', $p->id) }}"
                               class="text-green-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ route('penjamin.destroy', $p->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus penjamin ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">
                        Data penjamin belum ada
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION TENGAH --}}
    <div class="mt-6 flex justify-center">
        {{ $penjamins->withQueryString()->links() }}
    </div>

</div>

<script>
    function toggleClearButton() {
        const input = document.getElementById('searchInput');
        const btn = document.getElementById('clearBtn');
        btn.classList.toggle('hidden', input.value.trim() === '');
    }

    function clearSearch() {
        const input = document.getElementById('searchInput');
        input.value = '';
        toggleClearButton();
        input.focus();
    }

    document.addEventListener('DOMContentLoaded', toggleClearButton);
</script>
@endsection