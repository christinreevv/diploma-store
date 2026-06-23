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

        <div class="bg-white rounded-sm shadow p-6">

            <h3 class="text-lg font-semibold mb-4">
                Топ товаров
            </h3>

            <div class="space-y-2">

                @foreach ($topProducts as $item)
                    <div class="flex justify-between">

                        <span>
                            {{ $item->product?->title }}
                        </span>

                        <span class="font-semibold">
                            {{ $item->total }} шт.
                        </span>

                    </div>
                @endforeach

            </div>

        </div>

        <div class="bg-white rounded-sm shadow p-6">

            <h3 class="text-lg font-semibold mb-4">
                Популярные цвета
            </h3>

            <div class="space-y-2">

                @foreach ($topColors as $item)
                    <div class="flex justify-between">

                        <span>
                            {{ $item->color?->title }}
                        </span>

                        <span class="font-semibold">
                            {{ $item->total }} заказов
                        </span>

                    </div>
                @endforeach

            </div>

        </div>

        {{-- KPI --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-sm shadow">
                <p class="text-gray-500 text-sm">Всего товаров</p>
                <p class="text-2xl font-semibold">{{ $products->total() }}</p>
            </div>

            <div class="bg-white p-6 rounded-sm shadow">
                <p class="text-gray-500 text-sm">Активные</p>
                <p class="text-2xl font-semibold">
                    {{ $products->where('is_active', true)->count() }}
                </p>
            </div>

            <div class="bg-white p-6 rounded-sm shadow">
                <p class="text-gray-500 text-sm">Скрытые</p>
                <p class="text-2xl font-semibold">
                    {{ $products->where('is_active', false)->count() }}
                </p>
            </div>

        </div>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded-sm overflow-hidden">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">№</th>
                        <th class="px-4 py-3 text-left">Фото</th>
                        <th class="px-4 py-3 text-left">Название</th>
                        <th class="px-4 py-3 text-left">Цена</th>
                        <th class="px-4 py-3 text-left">Категория</th>
                        <th class="px-4 py-3 text-left">Статус</th>
                        <th class="px-4 py-3 text-center">Действия</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($products as $product)
                        @php
                            $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();

                            $minPrice = $product->sizes->min(fn($size) => $size->pivot->price);
                        @endphp

                        <tr class="hover:bg-gray-50 transition">

                            {{-- НУМЕРАЦИЯ С 1 --}}
                            <td class="px-4 py-3 text-gray-900 font-medium">
                                {{ $products->firstItem() + $loop->index }}
                            </td>

                            {{-- IMAGE --}}
                            <td class="px-4 py-3">
                                @if ($mainImage)
                                    <img src="{{ asset('storage/' . $mainImage->path) }}"
                                        class="w-14 h-18 object-cover rounded-sm">
                                @else
                                    <div
                                        class="w-14 h-18 bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                                        —
                                    </div>
                                @endif
                            </td>

                            {{-- TITLE --}}
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $product->title }}
                            </td>

                            {{-- PRICE --}}
                            <td class="px-4 py-3 text-gray-700">
                                {{ $minPrice ? number_format($minPrice, 0, '', ' ') . ' ₽' : '—' }}
                            </td>

                            {{-- CATEGORY --}}
                            <td class="px-4 py-3 text-gray-600">
                                {{ $product->category?->title ?? '—' }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-3">
                                <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">

                                        <span
                                            class="w-2 h-2 rounded-full
                {{ $product->is_active ? 'bg-gray-700' : 'bg-gray-300' }}"></span>

                                        {{ $product->is_active ? 'Активен' : 'Скрыт' }}

                                    </button>
                                </form>
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-4 py-3 text-center space-x-3">

                                <a href="{{ route('products.show', $product->slug) }}"
                                    class="text-blue-600 hover:underline">
                                    Смотреть
                                </a>

                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="text-yellow-600 hover:underline">
                                    Редактировать
                                </a>

                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" onclick="return confirm('Удалить товар?')"
                                        class="text-red-600 hover:underline">
                                        Удалить
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                Товары не найдены
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div class="bg-white p-6 rounded-sm shadow">
                    <p class="text-gray-500 text-sm">Выручка</p>
                    <p class="text-2xl font-semibold">
                        {{ number_format($totalRevenue, 0, '', ' ') }} ₽
                    </p>
                </div>

                <div class="bg-white p-6 rounded-sm shadow">
                    <p class="text-gray-500 text-sm">Заказов</p>
                    <p class="text-2xl font-semibold">
                        {{ $totalOrders }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-sm shadow">
                    <p class="text-gray-500 text-sm">Продано товаров</p>
                    <p class="text-2xl font-semibold">
                        {{ $totalSold }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-sm shadow">
                    <p class="text-gray-500 text-sm">Средний чек</p>
                    <p class="text-2xl font-semibold">
                        {{ $totalOrders ? number_format($totalRevenue / $totalOrders, 0, '', ' ') : 0 }} ₽
                    </p>
                </div>

            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $products->links() }}
        </div>

    </div>

@endsection
