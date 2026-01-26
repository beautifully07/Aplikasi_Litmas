<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Panel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 font-sans antialiased">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Content Area -->
    <div class="flex flex-col flex-1 bg-slate-100">

        <!-- Navbar -->
        @include('components.navbar')

        <!-- Page Header -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="px-6 py-4">
                    <h1 class="text-xl font-semibold text-gray-800">
                        {{ $header }}
                    </h1>
                </div>
            </header>
        @endisset

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-slate-100">
        {{ $slot }}
        </main>


    </div>
</div>
</body>
</html>