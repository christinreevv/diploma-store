@extends('layouts.admin')

@section('title', 'Избранное')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Избранное', 'url' => route('favorites.index')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
    <div class="container mx-auto py-6">

        @if ($favorites->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach ($favorites as $favorite)
                    @php
                        $product = $favorite->product;
                        $color = $favorite->productColor;

                        if (!$product || !$color) {
                            continue;
                        }

                        $images = $color?->images ?? collect();
                        $image = $images->where('is_main', true)->first() ?? $images->first();
                        $imageUrl = $image ? Storage::url($image->path) : asset('images/placeholder.jpg');

                        $price = $product->sizes->first()?->pivot->price ?? 0;

                        $productUrl = route('products.show', [
                            'slug' => $product->slug,
                            'color' => $color?->color?->code ?? null,
                        ]);

                        $inFavorite = true; // на странице избранного все элементы уже в избранном
                    @endphp

                    <div class="relative min-h-[28rem] flex flex-col">

                        {{-- IMAGE --}}
                        <a href="{{ $productUrl }}"
                            class="group block relative rounded-sm overflow-hidden shadow hover:shadow-lg transition">

                            <div class="aspect-[3/4] w-full overflow-hidden bg-white">
                                <img src="{{ $imageUrl }}"
                                    class="w-full h-full object-cover transition group-hover:opacity-80"
                                    alt="{{ $product->title }} ({{ $color?->color->title ?? '' }})">
                            </div>

                        </a>

                        {{-- INFO --}}
                        <div class="pt-3 flex items-start justify-between gap-2">

                            <div class="flex flex-col">
                                <h5 class="text-lg font-medium text-gray-800 truncate">
                                    <a href="{{ $productUrl }}" class="hover:underline">
                                        {{ $product->title }}
                                    </a>
                                </h5>

                                <p class="text-gray-700 font-semibold mt-1">
                                    {{ number_format($price, 0, ',', ' ') }} ₽
                                </p>
                            </div>

                            {{-- Избранное --}}
                            <form method="POST" action="{{ route('favorites.toggle', ['productColor' => $color->id]) }}">
                                @csrf

                                <button type="submit" onclick="event.stopPropagation()"
                                    class="favorite-btn active shrink-0">
                                    <img src="{{ asset('favorite1.svg') }}" class="w-6 h-6 favorite-img" alt="favorite">
                                </button>
                            </form>

                        </div>
                    </div>
                @endforeach

            </div>

            <div class="mt-6">
                {{ $favorites->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center text-center py-24 px-6">

                {{-- ICON --}}
                <div class="w-20 h-20 mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                        2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09
                        C13.09 3.81 14.76 3 16.5 3
                        19.58 3 22 5.42 22 8.5
                        c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                </div>

                {{-- TITLE --}}
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">
                    Ваш список избранного пуст
                </h2>

                {{-- TEXT --}}
                <p class="text-gray-500 max-w-md mb-6">
                    Вы пока не добавили ни одного товара в избранное.
                    Самое время выбрать что-то красивое 💫
                </p>

                {{-- CTA --}}
                <a href="{{ route('catalog.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white rounded-sm hover:bg-gray-800 transition">

                    Перейти в каталог

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>

                </a>

            </div>
            @endif

    </div>
@endsection
