@extends('layouts.app')

@section('content')

<div class="flex justify-center">
    <div class="w-full max-w-xl bg-white rounded-xl shadow p-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            ✏️ Edit User
        </h1>

        <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lengkap
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200"
                       required>
            </div>

            <!-- Username -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Username
                </label>
                <input type="text"
                       name="username"
                       value="{{ old('username', $user->username) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200"
                       required>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password Baru
                </label>
                <input type="password"
                       name="password"
                       placeholder="Kosongkan jika tidak ingin mengubah"
                       class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                <p class="text-xs text-gray-500 mt-1">
                    Biarkan kosong jika tidak ingin mengganti password
                </p>
            </div>

            <!-- Role -->
            @php
             $selectedRole = old('role', $user->getRoleNames()->first());
            @endphp

                <div>
                    <label class="block text-sm font-medium mb-1">Role</label>
                    <select name="role"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            required>

                        <option value="">-- Pilih Role --</option>

                        <option value="admin" {{ $selectedRole == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superuser" {{ $selectedRole == 'superuser' ? 'selected' : '' }}>Super User</option>
                        <option value="user" {{ $selectedRole == 'user' ? 'selected' : '' }}>User</option>

                    </select>
                </div>

            <!-- Tombol -->
            <div class="flex justify-between pt-4">
                <a href="{{ route('users.index') }}"
                   class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                    ← Kembali
                </a>

                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
