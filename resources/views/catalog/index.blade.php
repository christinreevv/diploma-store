@extends('layouts.admin')

@section('title', ucfirst($type ?? 'Каталог'))

@section('breadcrumbs')
    @php
        $pageType = $type ?? 'catalog';
        $breadcrumbs = [['label' => 'Главная', 'url' => url('/')]];

        switch ($pageType) {
            case 'collections':
                $breadcrumbs[] = ['label' => 'Коллекции', 'url' => route('collections.index')];
                break;
            case 'looks':
                $breadcrumbs[] = ['label' => 'Образы', 'url' => route('looks.index')];
                break;
            default:
                $breadcrumbs[] = ['label' => 'Каталог', 'url' => route('catalog.index')];
                break;
        }
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
    <div class="container mx-auto py-8">

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Фильтры --}}
            <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block">

                <div class="bg-white border border-gray-200 p-6 sticky top-6">

                    <form id="filter-form" method="GET" action="{{ route('catalog.index') }}" class="space-y-8">

                        <input type="hidden" name="sort" id="sort-input" value="{{ request('sort') }}">

                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <h3 class="text-sm uppercase tracking-[0.2em] font-semibold text-gray-900">
                                Фильтры
                            </h3>

                            <a href="{{ route('catalog.index') }}"
                                class="text-xs uppercase tracking-wide text-gray-500 hover:text-black transition">
                                Очистить
                            </a>
                        </div>

                        {{-- Категории --}}
                        <div class="border-b border-gray-100 pb-6">
                            <h4 class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold mb-4">
                                Категории
                            </h4>

                            <ul class="space-y-3">
                                @foreach ($categories as $cat)
                                    <li>
                                        <label class="inline-flex items-center cursor-pointer group">
                                            <input type="checkbox" name="category_id[]" value="{{ $cat->id }}"
                                                class="custom-checkbox"
                                                {{ in_array($cat->id, (array) request('category_id')) ? 'checked' : '' }}>

                                            <span class="ml-3 text-sm text-gray-700 transition group-hover:text-black">
                                                {{ $cat->title }}
                                            </span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Цвета --}}
                        <div>
                            <h4 class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold mb-4">
                                Цвета
                            </h4>

                            <ul class="space-y-3">
                                @foreach ($colors as $color)
                                    <li>
                                        <label class="inline-flex items-center cursor-pointer group">

                                            <input type="checkbox" name="color[]" value="{{ $color->code }}"
                                                class="custom-checkbox"
                                                {{ in_array($color->code, (array) request('color')) ? 'checked' : '' }}>

                                            <span class="w-4 h-4 ml-3 border border-gray-300 flex-shrink-0"
                                                style="background-color: {{ $color->code }}">
                                            </span>

                                            <span class="ml-3 text-sm text-gray-700 transition group-hover:text-black">
                                                {{ $color->title }}
                                            </span>

                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </form>

                </div>

            </aside>

            {{-- Контент --}}
            <main class="flex-1">

                {{-- Поиск --}}
                <div class="mb-6 relative">

                    <form id="search-form" method="GET" action="{{ route('catalog.index') }}" class="relative">

                        {{-- иконка --}}
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0a7 7 0 0114 0z" />
                        </svg>

                        {{-- input --}}
                        <input id="search-input" type="text" name="q" value="{{ request('q') }}"
                            autocomplete="off" placeholder="ПОИСК ТОВАРОВ"
                            class="w-full pl-10 pr-28 py-3 border border-gray-300 focus:border-black focus:outline-none transition">

                        {{-- КНОПКА ИСКАТЬ --}}
                        <button type="submit"
                            class="absolute pe-2 right-3 top-1/2 -translate-y-1/2 text-sm text-gray-700 hover:text-black">
                            ИСКАТЬ
                        </button>

                        {{-- КРЕСТИК --}}
                        <button type="button" id="clear-search"
                            class="hidden me-2 absolute right-20 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                            ✕
                        </button>

                    </form>

                    {{-- ПОДСКАЗКИ --}}
                    <div id="search-suggestions"
                        class="absolute z-50 w-full bg-white border border-gray-200 mt-2 hidden shadow-sm">

                    </div>

                </div>
                <div id="products-container" class="transition-opacity duration-200">

                    <div class="mt-4 mb-6 flex items-center justify-between">
                        <div class="flex items-center leading-none">
                            <x-view-switch />
                        </div>
                        <div class="flex items-center">
                            <div class="relative">

                                <select id="sort-select" name="sort"
                                    class="appearance-none bg-white border border-gray-200
            pl-4 pr-10 py-2 text-sm text-gray-700
            hover:border-gray-400
            focus:border-black focus:ring-0 focus:outline-none
            transition cursor-pointer">

                                    <option value="">По умолчанию</option>
                                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>
                                        Цена ↑
                                    </option>
                                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>
                                        Цена ↓
                                    </option>
                                    <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>
                                        Название А-Я
                                    </option>
                                    <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>
                                        Название Я-А
                                    </option>
                                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>
                                        Сначала новые
                                    </option>

                                </select>

                                {{-- стрелка --}}
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                            </div>
                        </div>


                    </div>

                    @include('catalog._products', ['products' => $products])

                </div>
            </main>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const filterForm = document.getElementById('filter-form');
            const productsContainer = document.getElementById('products-container');

            let timeout;
            let controller = null;

            const sortSelect = document.getElementById('sort-select');
            const hiddenSort = document.getElementById('sort-input');

            if (sortSelect && hiddenSort) {
                hiddenSort.value = sortSelect.value;
            }

            function getQueryString(form) {
                const formData = new FormData(form);
                return new URLSearchParams(formData).toString();
            }

            async function loadProducts() {

                // отменяем предыдущий запрос (ВАЖНО)
                if (controller) controller.abort();
                controller = new AbortController();

                productsContainer.style.opacity = '0.5';

                const url = filterForm.action + '?' + getQueryString(filterForm);

                try {
                    const response = await fetch(url, {
                        signal: controller.signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const html = await response.text();

                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newProducts = doc.querySelector('#products-container');

                    if (newProducts) {
                        productsContainer.innerHTML = newProducts.innerHTML;
                    }

                } catch (e) {
                    if (e.name !== 'AbortError') {
                        console.error(e);
                    }
                }

                productsContainer.style.opacity = '1';
            }

            function triggerLoad() {
                clearTimeout(timeout);
                timeout = setTimeout(loadProducts, 150);
            }

            // =========================
            // FILTERS
            // =========================
            filterForm.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', triggerLoad);
            });

            document.addEventListener('change', function(e) {
                if (e.target && e.target.id === 'sort-select') {

                    const hiddenSort = document.getElementById('sort-input');

                    if (hiddenSort) {
                        hiddenSort.value = e.target.value;
                    }

                    triggerLoad();
                }
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const input = document.getElementById('search-input');
            const clearBtn = document.getElementById('clear-search');
            const suggestions = document.getElementById('search-suggestions');

            let timeout;

            // показать/скрыть крестик
            function toggleClear() {
                if (input.value.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                    suggestions.classList.add('hidden');
                }
            }

            input.addEventListener('input', function() {

                toggleClear();

                clearTimeout(timeout);

                const query = input.value.trim();

                if (query.length < 2) {
                    suggestions.classList.add('hidden');
                    return;
                }

                timeout = setTimeout(async () => {

                    const res = await fetch(
                        `{{ route('catalog.index') }}?q=${query}&suggest=1`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                    const html = await res.text();

                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const items = doc.querySelectorAll('[data-suggestion]');

                    if (!items.length) {
                        suggestions.classList.add('hidden');
                        return;
                    }

                    suggestions.innerHTML = '';

                    items.forEach(item => {
                        suggestions.appendChild(item);
                    });

                    suggestions.classList.remove('hidden');

                }, 200);

            });

            // очистка
            clearBtn.addEventListener('click', function() {
                input.value = '';
                clearBtn.classList.add('hidden');
                suggestions.classList.add('hidden');
                input.focus();
            });

        });
    </script>
@endsection
