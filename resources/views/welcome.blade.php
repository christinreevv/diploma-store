@extends('layouts.admin')

@section('title', 'Главная')

@section('breadcrumbs')
    @php
        $breadcrumbs = [['label' => 'Главная', 'url' => url('/')]];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')

    <div class="container mx-auto py-8">

        @php
            $limitedImage = asset('images/placeholder.jpg');

            if (!empty($limitedProduct)) {
                $color = $limitedProduct->productColors->first();

                $image = $color?->images?->where('is_main', true)->first() ?? $color?->images?->first();

                if ($image) {
                    $limitedImage = Storage::url($image->path);
                }
            }
        @endphp

        @php
            $newImage = asset('images/placeholder.jpg');

            if (!empty($newProduct)) {
                $color = $newProduct->productColors->first();

                $image = $color?->images?->where('is_main', true)->first() ?? $color?->images?->first();

                if ($image) {
                    $newImage = Storage::url($image->path);
                }
            }
        @endphp

        {{-- Баннеры --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Лимитированная коллекция --}}
            <a href="{{ route('collections.limited') }}"
                class="group relative aspect-square overflow-hidden rounded-sm bg-gray-100">

                <img src="{{ $limitedImage ?? asset('images/placeholder.jpg') }}" alt="Лимитированная коллекция"
                    class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-105">

                <div class="absolute inset-0 bg-black/20"></div>

                <div class="absolute inset-0 flex flex-col justify-end p-8 text-white">
                    <span class="text-sm uppercase tracking-[0.25em] mb-2">
                        Эксклюзив
                    </span>

                    <h2 class="text-3xl lg:text-4xl font-light">
                        Лимитированная коллекция
                    </h2>

                    <p class="mt-3 text-sm opacity-90">
                        Уникальные модели ограниченного тиража
                    </p>
                </div>
            </a>

            {{-- Новинки --}}
            <a href="{{ route('new-arrivals') }}"
                class="group relative aspect-square overflow-hidden rounded-sm bg-gray-100">

                <img src="{{ $newImage }}" alt="Новинки"
                    class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-105">

                <div class="absolute inset-0 bg-black/20"></div>

                <div class="absolute inset-0 flex flex-col justify-end p-8 text-white">
                    <span class="text-sm uppercase tracking-[0.25em] mb-2">
                        New
                    </span>

                    <h2 class="text-3xl lg:text-4xl font-light">
                        Новинки
                    </h2>

                    <p class="mt-3 text-sm opacity-90">
                        Последние поступления сезона
                    </p>
                </div>
            </a>

        </div>

        {{-- О бренде --}}
        <section class="my-36">

            <div class="max-w-4xl mx-auto text-center">

                <span class="uppercase tracking-[0.25em] text-sm text-gray-500">
                    About Us
                </span>

                <h2 class="text-4xl lg:text-5xl font-light mt-4">
                    Одежда вне времени
                </h2>

                <p class="mt-8 text-lg text-gray-600 leading-relaxed">
                    Мы создаём коллекции для тех, кто ценит качество,
                    комфорт и современный стиль. Каждая модель проходит
                    тщательный отбор материалов и создаётся с вниманием
                    к деталям, чтобы оставаться актуальной не один сезон.
                </p>

            </div>

            <div class="mt-10 flex justify-center">

              <a href="{{ route('catalog.index') }}" class="catalog-btn">
    <span>Перейти в каталог</span>
</a>

            </div>

    </div>

    {{-- Преимущества --}}
    <section class="mb-36 ">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">

            <div class="text-center">
                <h3 class="text-xl font-medium mb-3">
                    Премиальные материалы
                </h3>

                <p class="text-gray-600">
                    Используем качественные ткани и фурнитуру
                    для максимального комфорта.
                </p>
            </div>

            <div class="text-center">
                <h3 class="text-xl font-medium mb-3">
                    Ограниченные коллекции
                </h3>

                <p class="text-gray-600">
                    Многие модели выпускаются небольшими тиражами,
                    сохраняя свою уникальность.
                </p>
            </div>

            <div class="text-center">
                <h3 class="text-xl font-medium mb-3">
                    Доставка по всей стране
                </h3>

                <p class="text-gray-600">
                    Быстрая отправка заказов и удобное отслеживание
                    каждой покупки.
                </p>
            </div>

        </div>

    </section>

    </section>
    {{-- Чаще всего заказывают --}}
    <div class="mt-20">

        <div class="mb-8">
            <span class="uppercase tracking-[0.25em] text-sm text-gray-500">
                Best Sellers
            </span>

            <h2 class="text-4xl font-light mt-2">
                Чаще всего заказывают
            </h2>
        </div>

        <div id="popular-slider" class="flex gap-6 overflow-x-auto pb-4 cursor-grab select-none">

            @foreach ($popularProducts as $product)
                @php
                    $color = $product->productColors->first();

                    $images = $color?->images ?? collect();

                    $image = $images->where('is_main', true)->first() ?? $images->first();

                    $imageUrl = $image ? Storage::url($image->path) : asset('images/placeholder.jpg');

                    $price = $product->sizes->first()?->pivot->price ?? 0;

                 $productUrl = route('products.show', [
    'slug' => $product->slug,
    'color' => $color?->color?->code,
]);
                @endphp

                <a href="{{ $productUrl }}" class="shrink-0 w-72">

                    <div class="aspect-[3/4] overflow-hidden rounded-sm bg-gray-100">

                        <img src="{{ $imageUrl }}" alt="{{ $product->title }}"
                            class="w-full h-full object-cover transition duration-500 hover:scale-105">

                    </div>

                    <div class="mt-3">

                        <h3 class="text-lg font-medium text-gray-900 truncate">
                            {{ $product->title }}
                        </h3>

                        <p class="mt-1 text-gray-700">
                            {{ number_format($price, 0, ',', ' ') }} ₽
                        </p>

                    </div>

                </a>
            @endforeach

        </div>

    </div>





    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const slider = document.getElementById('popular-slider');

            if (!slider) return;

            let isDown = false;
            let startX;
            let scrollLeft;

            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });

            slider.addEventListener('mouseleave', () => {
                isDown = false;
            });

            slider.addEventListener('mouseup', () => {
                isDown = false;
            });

            slider.addEventListener('mousemove', (e) => {

                if (!isDown) return;

                e.preventDefault();

                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 2;

                slider.scrollLeft = scrollLeft - walk;
            });

        });
    </script>

@endsection
