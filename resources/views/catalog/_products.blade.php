@php
    $selectedColors = (array) request('color', []);
@endphp

<main>
    {{-- Продукты --}}
    @if ($products->count())
<div data-products-grid class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($products as $product)
                @php
                    $colorVariants = $product->productColors;

                    if (!empty($selectedColors)) {
                        $colorVariants = $colorVariants->filter(function ($c) use ($selectedColors) {
                            return in_array($c->color->code, $selectedColors);
                        });
                    }

                    $colorVariants = $colorVariants->filter(fn($c) => $c->images->isNotEmpty());

                    if ($colorVariants->isEmpty()) {
                        continue;
                    }
                @endphp

                @foreach ($colorVariants as $color)
                    @php
                        $images = $color?->images ?? collect();
                        $image = $images->where('is_main', true)->first() ?? $images->first();

                        $imageUrl = $image ? Storage::url($image->path) : asset('images/placeholder.jpg');

                        $price = $product->sizes->first()?->pivot->price ?? 0;

                        $productUrl = route('products.show', [
                            'slug' => $product->slug,
                            'color' => Str::slug($color?->color?->title),
                        ]);

                        $inFavorite =
                            auth()->check() &&
                            auth()->user()->favorites()->where('product_color_id', $color->id)->exists();
                    @endphp

                    <div class="relative flex flex-col">

                        {{-- Hover только на изображении --}}
                        <a href="{{ $productUrl }}"
                            class="group block relative rounded-sm overflow-hidden shadow hover:shadow-lg transition">

                            {{-- Изображение --}}
                            <div class="aspect-[3/4] w-full overflow-hidden bg-white">
                                <img src="{{ $imageUrl }}" alt="{{ $product->title }} ({{ $color?->label ?? '' }})"
                                    class="w-full h-full object-cover transition duration-300 group-hover:opacity-70">
                            </div>
                        </a>

                        {{-- Информация --}}
                        <div class="pt-3 flex items-start justify-between gap-3">

                            <div class="flex flex-col text-left">
    <h5 class="text-sm lg:text-base font-normal text-gray-900 leading-snug tracking-wide">
        {{ $product->title }}
    </h5>

    <p class="mt-1 text-sm lg:text-base text-gray-500 font-light tracking-wide">
        {{ number_format($price, 0, ',', ' ') }} ₽
    </p>
</div>

                            {{-- Избранное --}}
                            <form method="POST" action="{{ route('favorites.toggle', $color->id) }}">
                                @csrf

                                <button type="submit" onclick="event.stopPropagation()"
                                    class="favorite-btn flex-shrink-0 w-8 h-8 flex items-center justify-center {{ $inFavorite ? 'active' : '' }}">
                                    <img src="{{ $inFavorite ? asset('favorite1.svg') : asset('favorite.png') }}"
                                        class="w-5 h-5 lg:w-6 lg:h-6 favorite-img pointer-events-none" alt="favorite">
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

        {{-- Пагинация --}}
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @else
        <p class="text-center text-gray-500">Нет товаров для отображения.</p>
    @endif
</main>
