@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>

    <a href="{{ route('users.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Tambah User
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
    <form method="GET" action="{{ route('users.index') }}" id="searchForm">
    <div style="position: relative;">
        <input
            type="text"
            id="searchInput"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari user..."
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
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold">No</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Nama</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Username</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Role</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($users as $user)

                @php
                    // Ambil role dari Spatie
                    $role = $user->getRoleNames()->first();
                @endphp

                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3">
                        {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                    </td>

                    <td class="px-4 py-3">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->username }}</td>

                    {{-- BADGE ROLE --}}
                    <td class="px-4 py-3">
                        @if($role)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($role === 'admin') bg-red-100 text-red-700
                                @elseif($role === 'superuser') bg-yellow-100 text-yellow-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ ucfirst($role) }}
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                Tidak ada role
                            </span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="px-4 py-3 flex gap-3 items-center">

                        {{-- EDIT --}}
                        <a href="{{ route('users.edit', $user) }}"
                           class="text-blue-600 hover:underline">
                            ✏️ Edit
                        </a>

                        {{-- RESET PASSWORD --}}
                        <form method="POST"
                              action="{{ route('users.reset-password', $user) }}"
                              onsubmit="return confirm('Reset password user ini?')">
                            @csrf
                            <button type="submit"
                                    class="text-gray-600 hover:underline">
                                🔁 Reset
                            </button>
                        </form>

                        {{-- DELETE --}}
                        @if(auth()->id() !== $user->id)
                            <form method="POST"
                                  action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:underline">
                                    🗑️ Hapus
                                </button>
                            </form>
                        @else
                            <span class="text-gray-400 text-sm italic">
                                (Akun sendiri)
                            </span>
                        @endif

                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="5"
                        class="px-4 py-6 text-center text-gray-500">
                        Belum ada user
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- PAGINATION --}}
<div class="mt-4">
    {{ $users->links() }}
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
        window.location.href = "{{ route('users.index') }}";
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