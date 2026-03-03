@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-800">
            Edit Pasal
        </h1>

        <a href="{{ route('pasal.index') }}"
           class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
            Kembali
        </a>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('pasal.update', $pasal->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- DATA PASAL --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <div>
                <label class="block font-semibold mb-1">Nomor Pasal</label>
                <input type="text"
                       name="nomor_pasal"
                       value="{{ old('nomor_pasal', $pasal->nomor_pasal) }}"
                       class="w-full border rounded px-3 py-2"
                       required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Judul</label>
                <input type="text"
                       name="judul"
                       value="{{ old('judul', $pasal->judul) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

        </div>

        <hr class="my-6">

        {{-- AYAT --}}
        <div id="ayat-wrapper">

            @foreach($pasal->ayats as $i => $ayat)
                <div class="ayat-item border rounded p-4 mb-4">

                    <h3 class="ayat-title font-bold text-lg mb-3">
                        Ayat {{ $loop->iteration }}
                    </h3>

                    {{-- Hidden ID --}}
                    <input type="hidden"
                           name="ayat[{{ $i }}][id]"
                           value="{{ $ayat->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block font-semibold mb-1">Nomor Ayat</label>
                            <input type="text"
                                   name="ayat[{{ $i }}][nomor]"
                                   value="{{ $ayat->nomor_ayat }}"
                                   class="w-full border rounded px-3 py-2"
                                   required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-semibold mb-1">Isi Ayat</label>
                            <textarea name="ayat[{{ $i }}][isi]"
                                      rows="3"
                                      class="w-full border rounded px-3 py-2"
                                      required>{{ $ayat->isi }}</textarea>
                        </div>

                    </div>

                    <button type="button"
                            onclick="hapusAyat(this)"
                            class="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Hapus Ayat
                    </button>

                </div>
            @endforeach

        </div>

        {{-- BUTTON TAMBAH AYAT --}}
        <button type="button"
                onclick="tambahAyat()"
                class="mb-6 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            + Tambah Ayat
        </button>

        {{-- BUTTON ACTION --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('pasal.index') }}"
               class="px-4 py-2 border rounded hover:bg-gray-100">
                Batal
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>


<script>
let index = {{ $pasal->ayats->count() }};

function tambahAyat(){

    let html = `
    <div class="ayat-item border rounded p-4 mb-4">

        <h3 class="ayat-title font-bold text-lg mb-3"></h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block font-semibold mb-1">Nomor Ayat</label>
                <input type="text"
                       name="ayat[${index}][nomor]"
                       class="w-full border rounded px-3 py-2"
                       required>
            </div>

            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Isi Ayat</label>
                <textarea name="ayat[${index}][isi]"
                          rows="3"
                          class="w-full border rounded px-3 py-2"
                          required></textarea>
            </div>

        </div>

        <button type="button"
                onclick="hapusAyat(this)"
                class="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
            Hapus Ayat
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