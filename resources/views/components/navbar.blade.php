<nav class="h-16 bg-white border-b flex items-center justify-between px-6">
    <div class="text-lg font-semibold text-slate-700">
        Dashboard
    </div>

    <div class="flex items-center gap-4">
        <span class="text-sm text-slate-600">
            {{ auth()->user()->email }}
        </span>

        <img
            src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}"
            class="w-9 h-9 rounded-full ring-2 ring-slate-300"
        >
    </div>
</nav>
