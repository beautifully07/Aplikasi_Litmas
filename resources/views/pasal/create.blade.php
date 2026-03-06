@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-6">
        Tambah Dasar Hukum
    </h2>


    {{-- ERROR VALIDATION --}}
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


        {{-- =====================================
            KLASIFIKASI HUKUM
        ====================================== --}}
        <div class="mb-6">

            <label class="font-semibold">
                Klasifikasi Hukum
            </label>

            <input
                type="text"
                name="nama_klasifikasi"
                value="{{ old('nama_klasifikasi') }}"
                placeholder="Contoh: KUHP / UU RI"
                class="w-full border rounded px-3 py-2 mt-1"
                required
            >

        </div>


        <hr class="my-6">


        {{-- =====================================
            WRAPPER PASAL
        ====================================== --}}
        <div id="pasal-wrapper">

            {{-- PASAL PERTAMA --}}
            <div class="pasal-item border rounded p-4 mb-6">

                <h3 class="pasal-title font-bold text-lg mb-3">
                    Pasal 1
                </h3>

                <input
                    type="text"
                    name="nomor_pasal"
                    placeholder="Nomor Pasal"
                    class="w-full border rounded px-3 py-2 mb-4"
                    required
                >


                {{-- AYAT WRAPPER --}}
                <div class="ayat-wrapper">

                    <div class="ayat-item border rounded p-3 mb-3">

                        <h4 class="ayat-title font-semibold mb-2">
                            Ayat 1
                        </h4>

                        <input
                            type="text"
                            name="pasal[0][ayat][0][nomor_ayat]"
                            placeholder="Nomor Ayat"
                            class="w-full border rounded px-3 py-2 mb-2"
                            required
                        >

                        <textarea
                            name="pasal[0][ayat][0][isi]"
                            rows="3"
                            placeholder="Isi Ayat"
                            class="w-full border rounded px-3 py-2"
                            required
                        ></textarea>

                    </div>

                </div>


                {{-- BUTTON TAMBAH AYAT --}}
                <button
                    type="button"
                    onclick="tambahAyat(this)"
                    class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                >
                    + Tambah Ayat
                </button>

            </div>

        </div>


        {{-- BUTTON TAMBAH PASAL --}}
        <button
            type="button"
            onclick="tambahPasal()"
            class="mb-6 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded"
        >
            + Tambah Pasal
        </button>


        {{-- ACTION BUTTON --}}
        <div class="flex gap-4">

            <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded"
            >
                Simpan
            </button>

            <a
                href="{{ route('pasal.index') }}"
                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
            >
                Kembali
            </a>

        </div>

    </form>

</div>



{{-- =====================================
    JAVASCRIPT DINAMIS
===================================== --}}
<script>

let pasalIndex = 1;


/* =================================
   TAMBAH PASAL
================================= */
function tambahPasal(){

    let html = `
    <div class="pasal-item border rounded p-4 mb-6">

        <h3 class="pasal-title font-bold text-lg mb-3">
            Pasal ${pasalIndex + 1}
        </h3>

        <input
            type="text"
            name="pasal[${pasalIndex}][nomor_pasal]"
            placeholder="Nomor Pasal"
            class="w-full border rounded px-3 py-2 mb-4"
            required
        >

        <div class="ayat-wrapper">

            <div class="ayat-item border rounded p-3 mb-3">

                <h4 class="ayat-title font-semibold mb-2">
                    Ayat 1
                </h4>

                <input
                    type="text"
                    name="pasal[${pasalIndex}][ayat][0][nomor_ayat]"
                    placeholder="Nomor Ayat"
                    class="w-full border rounded px-3 py-2 mb-2"
                    required
                >

                <textarea
                    name="pasal[${pasalIndex}][ayat][0][isi]"
                    rows="3"
                    placeholder="Isi Ayat"
                    class="w-full border rounded px-3 py-2"
                    required
                ></textarea>

            </div>

        </div>

        <button
            type="button"
            onclick="tambahAyat(this)"
            class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
        >
            + Tambah Ayat
        </button>

        <button
            type="button"
            onclick="hapusPasal(this)"
            class="mt-3 ml-3 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
        >
            Hapus Pasal
        </button>

    </div>
    `;

    document
        .getElementById('pasal-wrapper')
        .insertAdjacentHTML('beforeend', html);

    pasalIndex++;

}



/* =================================
   TAMBAH AYAT
================================= */
function tambahAyat(btn){

    let pasal = btn.closest('.pasal-item');
    let wrapper = pasal.querySelector('.ayat-wrapper');

    let ayatIndex = wrapper.children.length;

    let pasalIndex = pasal
        .querySelector('input')
        .name.match(/\d+/)[0];

    let html = `
    <div class="ayat-item border rounded p-3 mb-3">

        <h4 class="ayat-title font-semibold mb-2">
            Ayat ${ayatIndex + 1}
        </h4>

        <input
            type="text"
            name="pasal[${pasalIndex}][ayat][${ayatIndex}][nomor_ayat]"
            placeholder="Nomor Ayat"
            class="w-full border rounded px-3 py-2 mb-2"
            required
        >

        <textarea
            name="pasal[${pasalIndex}][ayat][${ayatIndex}][isi]"
            rows="3"
            placeholder="Isi Ayat"
            class="w-full border rounded px-3 py-2"
            required
        ></textarea>

        <button
            type="button"
            onclick="hapusAyat(this)"
            class="mt-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
        >
            Hapus Ayat
        </button>

    </div>
    `;

    wrapper.insertAdjacentHTML('beforeend', html);

}



/* =================================
   HAPUS PASAL
================================= */
function hapusPasal(btn){

    btn.closest('.pasal-item').remove();

}



/* =================================
   HAPUS AYAT
================================= */
function hapusAyat(btn){

    btn.closest('.ayat-item').remove();

}

</script>

@endsection