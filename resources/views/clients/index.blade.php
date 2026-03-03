@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Data Klien</h1>

    <a href="{{ route('clients.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition w-fit">
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
<!-- SEARCH -->
<form method="GET" action="{{ route('clients.index') }}" id="searchForm">
    <div style="position: relative;">
        <input
            type="text"
            id="searchInput"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari nama klien..."
            class="w-full h-11 rounded-lg
                   border border-blue-500
                   px-4 pr-12 text-sm
                   focus:outline-none"
            autocomplete="off"
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
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left">No</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">PK</th>
                <th class="px-4 py-3 text-left">No Register</th>
                <th class="px-4 py-3 text-left">JK</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Pekerjaan</th>
                <th class="px-4 py-3 text-left">Ciri Khusus</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($clients as $client)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        {{ ($clients->currentPage() - 1) * $clients->perPage() + $loop->iteration }}
                    </td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $client->nama }}
                    </td>
                    
                    <td class="px-4 py-3">
                        {{ $client->user->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $client->no_register }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $client->jenis_kelamin ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $client->status_perkawinan ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $client->pekerjaan ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ \Illuminate\Support\Str::limit($client->ciri_khusus, 30) ?? '-' }}
                    </td>

                    {{-- AKSI --}}
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-3">
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
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"
                        class="px-6 py-8 text-center text-gray-500">
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

    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearBtn');
    const searchForm = document.getElementById('searchForm');

    let debounceTimer;

    // 🔍 AUTO SEARCH dengan delay 500ms
    searchInput.addEventListener('input', function () {

        toggleClearButton();

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            searchForm.submit();
        }, 500); // delay supaya tidak spam request
    });

    // ❌ TOMBOL CLEAR
    function clearSearch() {
        searchInput.value = '';
        toggleClearButton();
        window.location.href = "{{ route('clients.index') }}";
    }

    // 👁️ Tampilkan / sembunyikan tombol X
    function toggleClearButton() {
        if (searchInput.value.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }

    // Saat halaman load
    document.addEventListener('DOMContentLoaded', toggleClearButton);
</script>
@endsection
