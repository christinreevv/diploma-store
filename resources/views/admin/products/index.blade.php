@extends('layouts.admin')

@section('title', 'Товары')

@section('breadcrumbs')
    @php
        $breadcrumbs = [['label' => 'Главная', 'url' => url('/')], ['label' => 'Товары', 'url' => '#']];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')

    <div class="container mx-auto py-10 space-y-8">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <h1 class="text-3xl font-light tracking-tight text-gray-600 mb-10">
                Все товары
            </h1>

            {{-- ACTIONS --}}
            <div class="flex flex-wrap gap-2">

                <a href="{{ route('admin.products.create') }}"
                    class="px-4 py-2 rounded-sm bg-black text-white text-sm hover:bg-gray-800 transition">
                    + Товар
                </a>

                <a href="{{ route('admin.colors.index') }}"
                    class="px-4 py-2 rounded-sm bg-white border text-gray-800 text-sm hover:bg-gray-50 transition">
                    Цвета
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="px-4 py-2 rounded-sm bg-white border text-gray-800 text-sm hover:bg-gray-50 transition">
                    Категории
                </a>

                <a href="{{ route('admin.category-matches.index') }}"
                    class="px-4 py-2 rounded-sm bg-white border text-gray-800 text-sm hover:bg-gray-50 transition">
                    Матчинг категорий
                </a>

                <a href="{{ route('admin.sizes.index') }}"
                    class="px-4 py-2 rounded-sm bg-white border text-gray-800 text-sm hover:bg-gray-50 transition">
                    Размеры
                </a>

            </div>

        </div>
        <div class="grid grid-cols-3 gap-12 py-8 border-y border-gray-200">

            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-gray-400">
                    Всего товаров
                </p>

                <p class="text-4xl font-light text-gray-900 mt-3">
                    {{ $products->total() }}
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-gray-400">
                    Активные
                </p>

                <p class="text-4xl font-light text-gray-900 mt-3">
                    {{ $products->where('is_active', true)->count() }}
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-gray-400">
                    Скрытые
                </p>

                <p class="text-4xl font-light text-gray-900 mt-3">
                    {{ $products->where('is_active', false)->count() }}
                </p>
            </div>

        </div>
        <div class="">

            <div class="mb-6">
                <h3 class="text-2xl font-light">
                    Топ товаров
                </h3>
            </div>

            <div class="flex gap-6 overflow-x-auto pb-4">

                @foreach ($topProducts as $item)
                    @php
                        $product = $item->product;

                        if (!$product) {
                            continue;
                        }

                        $color = $product->productColors->first();

                        $images = $color?->images ?? collect();

                        $image = $images->where('is_main', true)->first() ?? $images->first();

                        $imageUrl = $image ? Storage::url($image->path) : asset('images/placeholder.jpg');

                        $price = $product->sizes->first()?->pivot->price ?? 0;
                    @endphp

                    <a href="{{ route('products.show', $product->slug) }}" class="shrink-0 w-64 block group">

                        <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">

                            {{-- IMAGE --}}
                            <img src="{{ $imageUrl }}" alt="{{ $product->title }}"
                                class="w-full h-full object-cover transition duration-700 group-hover:scale-110 group-hover:brightness-90">

                            {{-- OVERLAY --}}
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition duration-500"></div>

                            {{-- BORDER EFFECT --}}
                            <div
                                class="absolute inset-0 border border-white/0 group-hover:border-white/40 transition duration-500">
                            </div>

                            {{-- QUICK VIEW --}}
                            <div
                                class="absolute inset-0 flex items-center justify-center opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition duration-500">

                                <span class="text-white text-xs tracking-[0.3em] uppercase">
                                    Quick view
                                </span>

                            </div>

                        </div>

                        {{-- TEXT --}}
                        <div class="mt-3">

                            <h4 class="font-medium text-gray-900 truncate group-hover:text-black transition">
                                {{ $product->title }}
                            </h4>

                            <div class="flex items-center justify-between mt-2">

                                <span class="text-gray-700">
                                    {{ number_format($price, 0, ',', ' ') }} ₽
                                </span>

                                <span class="text-xs px-2 py-1 rounded-full bg-black text-white">
                                    {{ $item->total }} шт.
                                </span>

                            </div>

                        </div>

                    </a>
                @endforeach

            </div>

        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 py-6 border-y border-gray-200">

            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-gray-400">
                    Выручка
                </p>

                <p class="mt-3 text-4xl font-light text-gray-900">
                    {{ number_format($totalRevenue, 0, '', ' ') }}
                </p>

                <span class="text-sm text-gray-500">
                    ₽
                </span>
            </div>

            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-gray-400">
                    Заказов
                </p>

                <p class="mt-3 text-4xl font-light text-gray-900">
                    {{ $totalOrders }}
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-gray-400">
                    Продано товаров
                </p>

                <p class="mt-3 text-4xl font-light text-gray-900">
                    {{ $totalSold }}
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-gray-400">
                    Средний чек
                </p>

                <p class="mt-3 text-4xl font-light text-gray-900">
                    {{ $totalOrders ? number_format($totalRevenue / $totalOrders, 0, '', ' ') : 0 }}
                </p>

                <span class="text-sm text-gray-500">
                    ₽
                </span>
            </div>

        </div>
        {{-- TABLE --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            @forelse($products as $product)
                @php
                    $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                    $minPrice = $product->sizes->min(fn($size) => $size->pivot->price);
                @endphp

                <div class="border border-gray-200 overflow-hidden">

                    {{-- фото --}}
                    <div class="aspect-[4/5] bg-gray-100 overflow-hidden relative group">

                        @if ($mainImage)
                            <img src="{{ asset('storage/' . $mainImage->path) }}"
                                class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                        @endif

                        {{-- затемнение при hover --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition"></div>

                        {{-- нижний градиент (дорогой эффект как в fashion ecom) --}}
                        <div
                            class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition">
                        </div>

                        {{-- кнопка появляется при hover --}}
                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <a href="{{ route('products.show', $product->slug) }}"
                                class="px-4 py-2 text-sm bg-white text-black hover:bg-black hover:text-white transition">
                                Смотреть
                            </a>
                        </div>

                    </div>

                    {{-- контент --}}
                    <div class="p-5">

                        <div class="flex items-start justify-between gap-3">

                          <div class="min-w-0 flex-1">
    <div class="text-xs uppercase tracking-wider text-gray-400">
        #{{ $products->firstItem() + $loop->index }}
    </div>

    <h3 class="mt-1 text-lg font-medium text-gray-900 leading-6 line-clamp-2 h-12 overflow-hidden">
        {{ $product->title }}
    </h3>
</div>

                           <button
    type="button"
    class="flex items-center gap-2 text-xs js-toggle-status"
    data-id="{{ $product->id }}"
    data-url="{{ route('admin.products.toggle-status', $product) }}"
>

    <span class="w-2 h-2 rounded-full status-dot
        {{ $product->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
    </span>

    <span class="status-text">
        {{ $product->is_active ? 'Активен' : 'Скрыт' }}
    </span>

</button>

                        </div>

                        <div class="mt-4 space-y-1 text-sm">

                            <div class="flex justify-between">
                                <span class="text-gray-500">Цена</span>
                                <span>{{ number_format($minPrice, 0, '', ' ') }} ₽</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Категория</span>
                                <span>{{ $product->category?->title ?? '—' }}</span>
                            </div>

                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">

                            <a href="{{ route('products.show', $product->slug) }}" class="text-gray-500 hover:text-black">
                                Смотреть
                            </a>

                            <a href="{{ route('admin.products.edit', $product) }}" class="text-gray-500 hover:text-black">
                                Изменить
                            </a>

                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Удалить товар?')" class="text-red-500 hover:text-red-700">
                                    Удалить
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-span-full py-20 text-center text-gray-400">
                    Товары не найдены
                </div>
            @endforelse

        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>

    </div>

    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.js-toggle-status').forEach(btn => {

        btn.addEventListener('click', async function () {

            const url = this.dataset.url;

            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ _method: 'PATCH' })
            });

            if (!res.ok) return;

            const data = await res.json();

            const dot = this.querySelector('.status-dot');
            const text = this.querySelector('.status-text');

            if (data.is_active) {
                dot.classList.remove('bg-gray-300');
                dot.classList.add('bg-green-500');
                text.textContent = 'Активен';
            } else {
                dot.classList.remove('bg-green-500');
                dot.classList.add('bg-gray-300');
                text.textContent = 'Скрыт';
            }
        });

    });

});
</script>

@endsection
