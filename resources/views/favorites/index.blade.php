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
            <p class="text-gray-500 text-center">У вас пока нет избранных товаров</p>
        @endif

    </div>
@endsection
