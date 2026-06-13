@php
    $selectedColors = (array) request('color', []);
@endphp

<main>
    {{-- Продукты --}}
    @if ($products->count())
        <div data-products-grid class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach ($products as $product)
                @php
                    $colorVariants = $product->productColors ?? collect();

                    // фильтр по выбранным цветам
                    if (!empty($selectedColors)) {
                        $colorVariants = $colorVariants->filter(fn($c) => in_array($c->color->code, $selectedColors));
                    }

                    // ❗ ВАЖНО: убираем цвета без изображений
                    $colorVariants = $colorVariants->filter(function ($c) {
                        return $c->images && $c->images->count() > 0;
                    });

                    // если после фильтра ничего не осталось — пропускаем товар
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
                            'product' => $product->slug,
                            'color' => $color?->key ?? 'default',
                        ]);
                        $inFavorite =
                            auth()->check() &&
                            auth()->user()->favorites()->where('product_color_id', $color->id)->exists();
                    @endphp

                    <div class="relative min-h-[28rem] flex flex-col">

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
                        <div class="pt-3 flex items-start justify-between gap-2">

                            <div class="flex flex-col text-left">
                                <h5 class="text-lg font-medium text-gray-800 truncate">
                                    {{ $product->title }}
                                </h5>

                                <p class="text-gray-700 font-semibold mt-1">
                                    {{ number_format($price, 0, ',', ' ') }} ₽
                                </p>
                            </div>

                            {{-- Избранное --}}
                            <form method="POST" action="{{ route('favorites.toggle', $color->id) }}">
                                @csrf

                                <button type="submit" onclick="event.stopPropagation()"
                                    class="favorite-btn shrink-0 {{ $inFavorite ? 'active' : '' }}">
                                    <img src="{{ $inFavorite ? asset('favorite1.svg') : asset('favorite.png') }}"
                                        class="w-6 h-6 favorite-img pointer-events-none" alt="favorite">
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

{{-- Скрипт избранного --}}
