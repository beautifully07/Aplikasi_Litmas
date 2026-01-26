<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Total Surat</h2>
            <p class="text-2xl mt-2 font-bold">120</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Surat Masuk</h2>
            <p class="text-2xl mt-2 font-bold">80</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold">Surat Keluar</h2>
            <p class="text-2xl mt-2 font-bold">40</p>
        </div>
    </div>
</x-app-layout>
