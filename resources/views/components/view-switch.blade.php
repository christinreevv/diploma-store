<div class="flex items-center justify-between mb-8">

    <div class="text-sm pe-3 text-gray-500">
      
    </div>

    <div class="flex gap-2">

        {{-- 1 карточка --}}
        <button onclick="setView(1)"
            class="p-2 border hover:bg-black hover:text-white transition">

            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                <rect x="4" y="4" width="16" height="16" stroke="currentColor" />
            </svg>

        </button>

        {{-- 3x3 --}}
        <button onclick="setView(3)"
            class="p-2 border hover:bg-black hover:text-white transition">

            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                <path d="M4 4H10V10H4V4Z" stroke="currentColor" />
                <path d="M14 4H20V10H14V4Z" stroke="currentColor" />
                <path d="M4 14H10V20H4V14Z" stroke="currentColor" />
                <path d="M14 14H20V20H14V14Z" stroke="currentColor" />
            </svg>

        </button>

        {{-- 5 колонок --}}
        <button onclick="setView(5)"
            class="p-2 border hover:bg-black hover:text-white transition">

            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="5" width="3" height="14" stroke="currentColor" />
                <rect x="8" y="5" width="3" height="14" stroke="currentColor" />
                <rect x="13" y="5" width="3" height="14" stroke="currentColor" />
                <rect x="18" y="5" width="3" height="14" stroke="currentColor" />
            </svg>

        </button>

    </div>

</div>
