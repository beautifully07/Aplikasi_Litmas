@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-6">Tambah Dasar Hukum</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pasal.store') }}" method="POST">
        @csrf

        {{-- DATA PASAL --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="font-semibold">Klasifikas Hukum</label>
                <input type="text"
                       name="judul"
                       value="{{ old('judul') }}"
                       class="w-full border rounded px-3 py-2 mt-1">
            </div>

            <div>
                <label class="font-semibold">Nomor Pasal</label>
                <input type="text"
                       name="nomor_pasal"
                       value="{{ old('nomor_pasal') }}"
                       class="w-full border rounded px-3 py-2 mt-1"
                       required>
            </div>
        </div>

        <hr class="my-6">

        {{-- AYAT --}}
        <div id="ayat-wrapper">

            <div class="ayat-item border rounded p-4 mb-4">

                <h3 class="ayat-title font-bold text-lg mb-3">
                    Ayat 1
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="font-semibold">Nomor Ayat</label>
                        <input type="text"
                               name="ayat[0][nomor]"
                               class="w-full border rounded px-3 py-2 mt-1"
                               required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-semibold">Isi Ayat</label>
                        <textarea name="ayat[0][isi]"
                                  rows="3"
                                  class="w-full border rounded px-3 py-2 mt-1"
                                  required></textarea>
                    </div>

                </div>

            </div>

        </div>

        {{-- BUTTON TAMBAH AYAT --}}
        <button type="button"
                onclick="tambahAyat()"
                class="mb-6 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            + Tambah Ayat
        </button>

        {{-- ACTION BUTTON --}}
        <div class="flex flex-wrap gap-4 items-center">

            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded">
                Simpan
            </button>

            <a href="{{ route('pasal.index') }}"
               class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                Kembali
            </a>

        </div>

    </form>
</div>


<script>
let index = 1;

function tambahAyat(){

    let html = `
    <div class="ayat-item border rounded p-4 mb-4">

        <h3 class="ayat-title font-bold text-lg mb-3"></h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="font-semibold">Nomor Ayat</label>
                <input type="text"
                       name="ayat[${index}][nomor]"
                       class="w-full border rounded px-3 py-2 mt-1"
                       required>
            </div>

            <div class="md:col-span-2">
                <label class="font-semibold">Isi Ayat</label>
                <textarea name="ayat[${index}][isi]"
                          rows="3"
                          class="w-full border rounded px-3 py-2 mt-1"
                          required></textarea>
            </div>

        </div>

        <button type="button"
                onclick="hapusAyat(this)"
                class="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
            Hapus
        </button>

    </div>
    `;

    document.getElementById('ayat-wrapper')
        .insertAdjacentHTML('beforeend', html);

    renumberAyat();
}

function hapusAyat(btn){
    btn.closest('.ayat-item').remove();
    renumberAyat();
}

/* =========================
   AUTO RENUMBER AYAT
========================= */
function renumberAyat(){

    const items = document.querySelectorAll('.ayat-item');

    items.forEach((item, i) => {

        item.querySelector('.ayat-title')
            .innerText = 'Ayat ' + (i + 1);

        item.querySelectorAll('input, textarea')
            .forEach(el => {
                if (el.name) {
                    el.name = el.name.replace(
                        /ayat\[\d+\]/,
                        `ayat[${i}]`
                    );
                }
            });

    });

    index = items.length;
}
</script>

@endsection