@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Data Dasar Hukum
    </h1>

    <a href="{{ route('pasal.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition w-fit">
        + Tambah Dasar Hukum
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



{{-- FILTER SECTION --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">

    {{-- SEARCH --}}
    <form method="GET"
          action="{{ route('pasal.index') }}"
          id="searchForm">

        <div class="relative">

            <input
                type="text"
                id="searchInput"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari pasal / klasifikasi hukum..."
                class="w-full h-11 rounded-lg border border-blue-500 px-4 pr-12 text-sm focus:outline-none"
                autocomplete="off"
            >

            {{-- CLEAR BUTTON --}}
            <button
                type="button"
                id="clearBtn"
                onclick="clearSearch()"
                class="hidden absolute right-3 top-1/2 -translate-y-1/2
                       h-6 w-6 flex items-center justify-center
                       rounded-full text-gray-400
                       hover:bg-gray-200 hover:text-red-500">
                ✕
            </button>

        </div>
    </form>


    {{-- PER PAGE --}}
    <form method="GET">

        <input type="hidden"
               name="search"
               value="{{ request('search') }}">

        <select
            name="per_page"
            onchange="this.form.submit()"
            class="px-3 py-2 pr-8 border rounded-lg">

            @foreach([10,15,20,30,50] as $size)
                <option value="{{ $size }}"
                    {{ request('per_page',10) == $size ? 'selected' : '' }}>
                    {{ $size }}
                </option>
            @endforeach

        </select>

    </form>

</div>



{{-- TABLE --}}
<div class="bg-white rounded-lg shadow overflow-x-auto">

    <table class="min-w-full text-sm">

        {{-- TABLE HEADER --}}
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left">No</th>
                <th class="px-4 py-3 text-left">Klasifikasi Hukum</th>
                <th class="px-4 py-3 text-left">Nomor Pasal</th>
                <th class="px-4 py-3 text-left">Daftar Ayat</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>


        {{-- TABLE BODY --}}
        <tbody class="divide-y">

        @forelse ($pasals as $pasal)

            <tr class="hover:bg-gray-50">

                {{-- NOMOR --}}
                <td class="px-4 py-3">
                    {{ ($pasals->currentPage() - 1) * $pasals->perPage() + $loop->iteration }}
                </td>


                {{-- KLASIFIKASI HUKUM --}}
                <td class="px-4 py-3">
                    {{ $pasal->klasifikasiHukum->nama_klasifikasi ?? '-' }}
                </td>


                {{-- NOMOR PASAL --}}
                <td class="px-4 py-3 font-semibold text-gray-800">
                    Pasal {{ $pasal->nomor_pasal }}
                </td>


                {{-- DAFTAR AYAT --}}
                <td class="px-4 py-3">

                    @if($pasal->ayats->count())

                        <div class="flex flex-wrap gap-2">

                            @foreach($pasal->ayats->take(3) as $ayat)
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                    Ayat ({{ $ayat->nomor_ayat }})
                                </span>
                            @endforeach

                            @if($pasal->ayats->count() > 3)
                                <span class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded-full">
                                    +{{ $pasal->ayats->count() - 3 }} lainnya
                                </span>
                            @endif

                        </div>

                    @else
                        <span class="text-gray-400">
                            Belum ada ayat
                        </span>
                    @endif

                </td>


                {{-- AKSI --}}
                <td class="px-4 py-3 text-center">

                    <div class="flex justify-center gap-3">

                        <a href="{{ route('pasal.show', $pasal) }}"
                           class="text-blue-600 hover:underline">
                            Detail
                        </a>

                        <a href="{{ route('pasal.edit', $pasal) }}"
                           class="text-green-600 hover:underline">
                            Edit
                        </a>

                        <form method="POST"
                              action="{{ route('pasal.destroy', $pasal) }}"
                              onsubmit="return confirm('Hapus pasal ini?')">

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
                <td colspan="5"
                    class="px-6 py-8 text-center text-gray-500">
                    Data dasar hukum belum tersedia
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>



{{-- PAGINATION --}}
<div class="mt-4">
    {{ $pasals->appends(request()->query())->links() }}
</div>



{{-- SCRIPT --}}
<script>

const searchInput = document.getElementById('searchInput');
const clearBtn = document.getElementById('clearBtn');
const searchForm = document.getElementById('searchForm');

let debounceTimer;


// AUTO SEARCH
searchInput.addEventListener('input', function () {

    toggleClearButton();

    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        searchForm.submit();
    }, 500);

});


// CLEAR SEARCH
function clearSearch() {

    searchInput.value = '';

    toggleClearButton();

    window.location.href = "{{ route('pasal.index') }}";

}


// TOGGLE CLEAR BUTTON
function toggleClearButton() {

    if (searchInput.value.length > 0) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }

}


// ON LOAD
document.addEventListener('DOMContentLoaded', toggleClearButton);

</script>

@endsection