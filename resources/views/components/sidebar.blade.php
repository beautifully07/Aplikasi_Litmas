<aside class="w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white flex flex-col shadow-xl">

    <!-- Brand -->
    <div class="h-16 flex items-center justify-center border-b border-slate-700">
        <span class="text-lg font-bold tracking-wide text-white">
            Admin Panel
        </span>
    </div>

    <!-- User -->
    <div class="flex items-center gap-3 px-4 py-4">
        <img
            src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=2563EB&color=fff"
            class="w-11 h-11 rounded-full ring-2 ring-blue-500"
        >
        <div>
            <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
            <span class="text-xs text-green-400 flex items-center gap-1">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                Online
            </span>
        </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-3 mt-2 space-y-1">

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-2 rounded-xl transition-all duration-200
           {{ request()->routeIs('dashboard')
                ? 'bg-blue-600 shadow-lg shadow-blue-600/30'
                : 'hover:bg-slate-700' }}">
            🏠 Dashboard
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-700 transition">
            📄 Data Surat
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-700 transition">
            👤 Manajemen User
        </a>

    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-slate-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="w-full flex items-center justify-center gap-2 py-2 rounded-xl
                       text-red-400 hover:bg-red-500/10 transition">
                🚪 Logout
            </button>
        </form>
    </div>

</aside>
