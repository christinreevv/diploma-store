<div class="flex items-center gap-2">

    {{-- 1 карточка --}}
    <button onclick="setView(1)"
        class="p-2 border hover:bg-black hover:text-white transition flex items-center justify-center">

        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
            <rect x="4" y="4" width="16" height="16" stroke="currentColor" />
        </svg>

    </button>

    {{-- 3x3 --}}
    <button onclick="setView(3)"
        class="p-2 border border-gray-300 hover:bg-black hover:text-white transition hidden lg:flex items-center justify-center">

        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">

            <rect x="4" y="4" width="6" height="6" />
            <rect x="14" y="4" width="6" height="6" />
            <rect x="4" y="14" width="6" height="6" />
            <rect x="14" y="14" width="6" height="6" />

        </svg>

    </button>

    {{-- 5 колонок --}}
    <button onclick="setView(5)"
        class="p-2 border hover:bg-black hover:text-white transition flex items-center justify-center">

        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="5" width="3" height="14" stroke="currentColor" />
            <rect x="8" y="5" width="3" height="14" stroke="currentColor" />
            <rect x="13" y="5" width="3" height="14" stroke="currentColor" />
            <rect x="18" y="5" width="3" height="14" stroke="currentColor" />
        </svg>

    </button>

</div>
