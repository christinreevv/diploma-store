@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

    <div class="mb-12">
        <h1 class="text-4xl font-light tracking-tight text-gray-900">
            Матчинг товаров
        </h1>

        <p class="mt-2 text-gray-500">
            Настройте сочетания категорий и цветов для блока «Покупают вместе».
        </p>
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

                <form
                    method="POST"
                    action="{{ route('admin.category-matches.update', $category) }}"
                    class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition"
                >
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

                                <input
                                    type="checkbox"
                                    name="matched_categories[]"
                                    value="{{ $related->id }}"
                                    class="peer hidden"
                                    {{ $category->matches->contains($related->id) ? 'checked' : '' }}
                                >

                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full border
                                    text-sm transition-all
                                    peer-checked:bg-black
                                    peer-checked:text-white
                                    peer-checked:border-black
                                    hover:border-black"
                                >
                                    {{ $related->title }}
                                </span>

                            </label>

                        @endforeach

                    </div>

                    <button
                        class="mt-6 px-5 py-2.5 rounded-xl bg-black text-white text-sm hover:bg-gray-800 transition"
                    >
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

                <form
                    method="POST"
                    action="{{ route('admin.color-matches.update', $color) }}"
                    class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition"
                >
                    @csrf

                    <div class="flex items-center gap-3 mb-5">

                        <div
                            class="w-7 h-7 rounded-full border border-gray-300"
                            style="background: {{ $color->code }}"
                        ></div>

                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $color->title }}
                        </h3>

                    </div>

                    <div class="flex flex-wrap gap-2">

                        @foreach ($colors as $related)

                            @continue($related->id === $color->id)

                            <label class="cursor-pointer">

                                <input
                                    type="checkbox"
                                    name="matched_colors[]"
                                    value="{{ $related->id }}"
                                    class="peer hidden"
                                    {{ $color->matches->contains($related->id) ? 'checked' : '' }}
                                >

                                <span
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full border
                                    text-sm transition-all
                                    peer-checked:bg-black
                                    peer-checked:text-white
                                    peer-checked:border-black
                                    hover:border-black"
                                >

                                    <span
                                        class="w-3 h-3 rounded-full border border-white/30"
                                        style="background: {{ $related->code }}"
                                    ></span>

                                    {{ $related->title }}

                                </span>

                            </label>

                        @endforeach

                    </div>

                    <button
                        class="mt-6 px-5 py-2.5 rounded-xl bg-black text-white text-sm hover:bg-gray-800 transition"
                    >
                        Сохранить
                    </button>

                </form>

            @endforeach

        </div>

    </div>

</div>
@endsection
