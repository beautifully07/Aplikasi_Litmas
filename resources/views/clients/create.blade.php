@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow">

    <h1 class="text-2xl font-bold mb-6">Tambah Data Klien</h1>

    <form method="POST" action="{{ route('clients.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- NAMA --}}
            <div>
                <label class="block mb-1 font-semibold">Nama</label>
                <input type="text" name="nama"
                       value="{{ old('nama') }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            {{-- NO REGISTER --}}
            <div>
                <label class="block mb-1 font-semibold">No Register</label>
                <input type="text" name="no_register"
                       value="{{ old('no_register') }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            {{-- TEMPAT LAHIR --}}
            <div>
                <label class="block mb-1 font-semibold">Tempat Lahir</label>
                <input type="text" name="tempat_lahir"
                       value="{{ old('tempat_lahir') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- TANGGAL LAHIR --}}
            <div>
                <label class="block mb-1 font-semibold">Tanggal Lahir</label>
                <input type="text"
                       id="tanggal_lahir"
                       name="tanggal_lahir"
                       placeholder="DD-MM-YYYY"
                       value="{{ old('tanggal_lahir') }}"
                       class="w-full border rounded px-3 py-2"
                       autocomplete="off">
            </div>

            {{-- USIA --}}
            <div>
                <label class="block mb-1 font-semibold">Usia</label>
                <input type="text"
                       id="usia"
                       name="usia"
                       class="w-full border rounded px-3 py-2 bg-gray-100"
                       readonly>
            </div>

            {{-- JENIS KELAMIN --}}
            <div>
                <label class="block mb-1 font-semibold">Jenis Kelamin</label>
                <select name="jenis_kelamin"
                        class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            {{-- AGAMA --}}
            <div>
                <label class="block mb-1 font-semibold">Agama</label>
                <select name="agama"
                        class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih --</option>
                    @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                        <option value="{{ $agama }}">{{ $agama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- STATUS PERKAWINAN --}}
            <div>
                <label class="block mb-1 font-semibold">Status Perkawinan</label>
                <select name="status_perkawinan"
                        class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih --</option>
                    @foreach(['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            {{-- SUKU --}}
            <div>
                <label class="block mb-1 font-semibold">Suku</label>
                <input type="text" name="suku"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- KEBANGSAAN --}}
            <div>
                <label class="block mb-1 font-semibold">Kebangsaan</label>
                <input type="text" name="kebangsaan"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- KEWARGANEGARAAN --}}
            <div>
                <label class="block mb-1 font-semibold">Kewarganegaraan</label>
                <select name="kewarganegaraan"
                        class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih --</option>
                    <option value="WNI">WNI</option>
                    <option value="WNA">WNA</option>
                </select>
            </div>

            {{-- PENDIDIKAN --}}
            <div>
                <label class="block mb-1 font-semibold">Pendidikan</label>
                <input type="text" name="pendidikan"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- PEKERJAAN --}}
            <div>
                <label class="block mb-1 font-semibold">Pekerjaan</label>
                <input type="text" name="pekerjaan"
                       class="w-full border rounded px-3 py-2">
            </div>

        </div>

        {{-- ALAMAT --}}
        <div class="mt-4">
            <label class="block mb-1 font-semibold">Alamat</label>
            <textarea name="alamat"
                      class="w-full border rounded px-3 py-2"
                      rows="3"></textarea>
        </div>

        {{-- CIRI KHUSUS --}}
        <div class="mt-4">
            <label class="block mb-1 font-semibold">Ciri Khusus</label>
            <textarea name="ciri_khusus"
                      class="w-full border rounded px-3 py-2"
                      rows="3"></textarea>
        </div>

        {{-- BUTTON --}}
        <div class="mt-6 flex gap-3">
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Simpan
            </button>

            <a href="{{ route('clients.index') }}"
               class="px-5 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Batal
            </a>
        </div>

    </form>
</div>

{{-- SCRIPT --}}
<script>
document.getElementById('tanggal_lahir').addEventListener('change', hitungUsia);
document.getElementById('tanggal_lahir').addEventListener('keyup', hitungUsia);

function hitungUsia() {
    let input = document.getElementById('tanggal_lahir').value;
    if (!input) return;

    let parts = input.split('-');
    if (parts.length !== 3) return;

    let day = parseInt(parts[0]);
    let month = parseInt(parts[1]) - 1;
    let year = parseInt(parts[2]);

    let birthDate = new Date(year, month, day);
    if (isNaN(birthDate)) return;

    let today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    let m = today.getMonth() - birthDate.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    document.getElementById('usia').value = age;
}
</script>

@endsection
