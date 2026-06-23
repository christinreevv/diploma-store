@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        <div class="mb-12">

            {{-- стрелка назад --}}
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-black transition mb-6">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>

                Назад
            </a>

            <div class="flex items-center gap-2">

                <h2 class="text-2xl font-light text-gray-600">
                    Матчинг товаров
                </h2>

                {{-- tooltip --}}
                <div class="relative">

                    <button type="button"
                        class="w-5 h-5 flex items-center justify-center text-xs border border-gray-300 rounded-full text-gray-500 cursor-help"
                        onmouseenter="this.nextElementSibling.classList.remove('hidden')"
                        onmouseleave="this.nextElementSibling.classList.add('hidden')">

                        ?

                    </button>
                    <div
                        class="hidden absolute left-full ml-3 top-7
    w-80 bg-black text-white text-xs rounded-lg px-3 py-2 z-50 shadow-lg">

                        Настройте сочетания категорий и цветов для блока «Покупают вместе».

                    </div>

                </div>

            </div>

        </div>

        {{-- КАТЕГОРИИ --}}
        <div class="mb-16">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-medium">
                    Категории
                </h2>

                <span class="text-sm text-gray-400">
                    {{ $categories->count() }} категорий
                </span>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">

                @foreach ($categories as $category)
                    <form method="POST" action="{{ route('admin.category-matches.update', $category) }}"
                        class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                        @csrf

                        <div class="flex items-center justify-between mb-5">

                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $category->title }}
                            </h3>

                            <span class="text-xs uppercase tracking-wider text-gray-400">
                                Category
                            </span>

                        </div>

                        <div class="flex flex-wrap gap-2">

                            @foreach ($categories as $related)
                                @continue($related->id === $category->id)

                                <label class="cursor-pointer">

                                    <input type="checkbox" name="matched_categories[]" value="{{ $related->id }}"
                                        class="peer hidden"
                                        {{ $category->matches->contains($related->id) ? 'checked' : '' }}>

                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full border
                                    text-sm transition-all
                                    peer-checked:bg-black
                                    peer-checked:text-white
                                    peer-checked:border-black
                                    hover:border-black">
                                        {{ $related->title }}
                                    </span>

                                </label>
                            @endforeach

                        </div>

                        <button
                            class="mt-6 px-5 py-2.5 rounded-xl bg-black text-white text-sm hover:bg-gray-800 transition">
                            Сохранить
                        </button>

                    </form>
                @endforeach

            </div>

        </div>

        {{-- ЦВЕТА --}}
        <div>

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-medium">
                    Цвета
                </h2>

                <span class="text-sm text-gray-400">
                    {{ $colors->count() }} цветов
                </span>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">

                @foreach ($colors as $color)
                    <form method="POST" action="{{ route('admin.color-matches.update', $color) }}"
                        class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                        @csrf

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-7 h-7 rounded-full border border-gray-300"
                                style="background: {{ $color->code }}"></div>

                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $color->title }}
                            </h3>

                        </div>

                        <div class="flex flex-wrap gap-2">

                            @foreach ($colors as $related)
                                @continue($related->id === $color->id)

                                <label class="cursor-pointer">

                                    <input type="checkbox" name="matched_colors[]" value="{{ $related->id }}"
                                        class="peer hidden" {{ $color->matches->contains($related->id) ? 'checked' : '' }}>

                                    <span
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full border
                                    text-sm transition-all
                                    peer-checked:bg-black
                                    peer-checked:text-white
                                    peer-checked:border-black
                                    hover:border-black">

                                        <span class="w-3 h-3 rounded-full border border-white/30"
                                            style="background: {{ $related->code }}"></span>

                                        {{ $related->title }}

                                    </span>

                                </label>
                            @endforeach

                        </div>

                        <button
                            class="mt-6 px-5 py-2.5 rounded-xl bg-black text-white text-sm hover:bg-gray-800 transition">
                            Сохранить
                        </button>

                    </form>
                @endforeach

            </div>

        </div>

    </div>
@endsection
