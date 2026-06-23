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

                        <div class="aspect-[3/4] overflow-hidden rounded-sm bg-gray-100">

                            <img src="{{ $imageUrl }}" alt="{{ $product->title }}"
                                class="w-full h-full object-cover transition duration-500 group-hover:scale-105">

                        </div>

                        <div class="mt-3">

                            <h4 class="font-medium text-gray-900 truncate">
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


        {{-- TABLE --}}
   <div class="border border-gray-200 overflow-hidden">

    <table class="w-full">

        <thead class="border-b border-gray-200 bg-gray-50">

            <tr class="text-xs uppercase tracking-[0.15em] text-gray-500">

                <th class="px-6 py-4 text-left font-medium">№</th>
                <th class="px-6 py-4 text-left font-medium">Фото</th>
                <th class="px-6 py-4 text-left font-medium">Название</th>
                <th class="px-6 py-4 text-left font-medium">Цена</th>
                <th class="px-6 py-4 text-left font-medium">Категория</th>
                <th class="px-6 py-4 text-left font-medium">Статус</th>
                <th class="px-6 py-4 text-right font-medium">Действия</th>

            </tr>

        </thead>

        <tbody>

            @forelse ($products as $product)

                @php
                    $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();

                    $minPrice = $product->sizes->min(fn($size) => $size->pivot->price);
                @endphp

                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">

                    {{-- ID --}}
                    <td class="px-6 py-5 text-gray-400 text-sm">
                        {{ $products->firstItem() + $loop->index }}
                    </td>

                    {{-- IMAGE --}}
                    <td class="px-6 py-5">

                        @if ($mainImage)
                            <img src="{{ asset('storage/' . $mainImage->path) }}"
                                class="w-14 h-20 object-cover">
                        @else
                            <div class="w-14 h-20 bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                —
                            </div>
                        @endif

                    </td>

                    {{-- TITLE --}}
                    <td class="px-6 py-5">

                        <div class="font-medium text-gray-900">
                            {{ $product->title }}
                        </div>

                    </td>

                    {{-- PRICE --}}
                    <td class="px-6 py-5 font-medium text-gray-900">

                        {{ $minPrice ? number_format($minPrice, 0, '', ' ') . ' ₽' : '—' }}

                    </td>

                    {{-- CATEGORY --}}
                    <td class="px-6 py-5 text-gray-600">

                        {{ $product->category?->title ?? '—' }}

                    </td>

                    {{-- STATUS --}}
                    <td class="px-6 py-5">

                        <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                class="inline-flex items-center gap-2 text-sm">

                                <span
                                    class="w-2 h-2 rounded-full
                                    {{ $product->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                </span>

                                <span class="text-gray-700">
                                    {{ $product->is_active ? 'Активен' : 'Скрыт' }}
                                </span>

                            </button>

                        </form>

                    </td>

                    {{-- ACTIONS --}}
                    <td class="px-6 py-5">

                        <div class="flex justify-end items-center gap-5 text-sm">

                            <a href="{{ route('products.show', $product->slug) }}"
                                class="text-gray-500 hover:text-black transition">
                                Смотреть
                            </a>

                            <a href="{{ route('admin.products.edit', $product) }}"
                                class="text-gray-500 hover:text-black transition">
                                Изменить
                            </a>

                            <form action="{{ route('admin.products.destroy', $product) }}"
                                method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="return confirm('Удалить товар?')"
                                    class="text-red-500 hover:text-red-700 transition">
                                    Удалить
                                </button>
                            </form>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="py-20 text-center text-gray-400">
                        Товары не найдены
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

<div class="border-t border-gray-200">
    {{ $products->links() }}
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
    </div>

@endsection
