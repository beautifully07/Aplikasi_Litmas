@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Dashboard
</h1>

<a href="{{ route('litmas.index') }}"
   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium
          text-green-700 border border-green-600 rounded-md
          hover:bg-green-200 transition mb-6">
    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-4 h-4"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4" />
    </svg>
    Buat Litmas
</a>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Litmas</h3>
        <p class="text-3xl font-bold">{{ $totalLitmas ?? 0 }}</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Klien</h3>
        <p class="text-3xl font-bold">{{ $totalKlien ?? 0 }}</p>
    </div>

</div>

@endsection