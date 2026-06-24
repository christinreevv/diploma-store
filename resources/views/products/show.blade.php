@extends('layouts.admin')

@section('title', $product->name)

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Каталог', 'url' => route('catalog.index')],
            ['label' => $product->title, 'url' => '#'],
        ];
    @endphp
    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
    @php
        $imagesByColor = [];
        $generalImages = [];

        foreach ($product->productColors as $productColor) {
            $colorId = $productColor->color->id;

            $imagesByColor[$colorId] = $productColor->images->map(fn($img) => Storage::url($img->path))->toArray();
        }

        foreach ($product->images as $img) {
            $generalImages[] = Storage::url($img->path);
        }

        $colors = $product->productColors
            ->map(
                fn($pc) => [
                    'key' => $pc->color->code,
                    'label' => $pc->color->title,
                ],
            )
            ->toArray();

        $selectedColorKey = $selectedColor?->color?->id;
        $initialImages =
            $selectedColorKey && isset($imagesByColor[$selectedColorKey])
                ? $imagesByColor[$selectedColorKey]
                : $generalImages;

        $hasMultipleImages = count($initialImages) > 1;

        $initialImage = $initialImages[0] ?? asset('images/placeholder.jpg');
    @endphp

    @php
        $hasMultipleImages = isset($imagesByColor[$selectedColorKey]) && count($imagesByColor[$selectedColorKey]) > 1;
    @endphp

    @php
        $inFavorite =
            auth()->check() &&
            $selectedColor &&
            auth()->user()->favorites()->where('product_color_id', $selectedColor->id)->exists();
    @endphp

    <div class="flex flex-col min-h-[calc(100vh-120px)]">
        <div class="container mx-auto px-4 py-10 flex-1 grid grid-cols-1 md:grid-cols-2 gap-12">

            {{-- Галерея --}}
            {{-- Галерея --}}
            <div id="imageWrapper" class="relative rounded-lg bg-gray-50 overflow-hidden h-[80vh] flex">

                {{-- Кнопка влево --}}
                <button id="prevImage"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-10
               p-2 rounded-md transition
               {{ $hasMultipleImages ? '' : 'hidden' }}">
                    ‹
                </button>

                {{-- Картинка --}}
                <img id="productImage" src="{{ $initialImage }}"
                    class="w-full h-full object-contain transition-all duration-300">

                {{-- Кнопка вправо --}}
                <button id="nextImage"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-10
               p-2 rounded-md transition
               {{ $hasMultipleImages ? '' : 'hidden' }}">
                    ›
                </button>

            </div>

            {{-- Информация --}}
            <div class="flex flex-col justify-start space-y-6">

                {{-- Название и цена --}}
                <div>
                    <h1 class="text-3xl font-semibold mb-3">{{ $product->title }}</h1>
                    @if ($product->sizes->first()?->pivot->price)
                        <p class="text-2xl font-bold mb-6">
                            {{ number_format($product->sizes->first()->pivot->price, 0, ',', ' ') }} ₽</p>
                    @endif
                </div>

                {{-- Размеры --}}
                @if ($product->sizes->count())
                    <div id="sizeContainer">
                        <h4 class="font-medium mb-2 text-sm text-gray-600 uppercase">Размер</h4>

                        <div id="sizeOptions" class="flex flex-wrap gap-2">
                            @foreach ($product->sizes as $size)
                                <button type="button"
                                    class="size-option border border-gray-300 rounded-md px-4 py-2 text-sm transition transform hover:-translate-y-0.5"
                                    data-id="{{ $size->id }}">
                                    {{ strtoupper($size->title) }}
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="size_id" id="selectedSize">
                    </div>
                @endif

                @error('size_id')
                    <p class="text-red-500 text-sm mt-2">Выберите размер</p>
                @enderror

                {{-- Цвета --}}
                @if ($product->productColors->count())
                    <div>
                        <h4 class="font-medium mb-2 text-sm text-gray-600 uppercase">Цвет</h4>
                        <div class="flex items-center gap-4 flex-wrap" id="colorOptions">
                            @foreach ($product->productColors as $productColor)
                                @php
                                    $color = $productColor->color;
                                    $hasImages = $productColor->images->count() > 0;
                                @endphp

                                <div class="flex items-center gap-2 color-option {{ !$hasImages ? 'opacity-30 cursor-not-allowed pointer-events-none' : 'cursor-pointer' }}"
                                    data-color="{{ $productColor->color->id }}">
                                    <div class="w-6 h-6 rounded-full border border-gray-300"
                                        style="background-color: {{ $color->code }}"></div>

                                    <span class="text-sm text-gray-700">
                                        {{ ucfirst($color->title) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="border-t border-gray-200 pt-6 space-y-3">

                    @foreach ([
            'details' => 'Детали',
            'care' => 'Уход',
            'delivery' => 'Доставка',
            'ask' => 'Задать вопрос',
        ] as $id => $label)
                        <div class="border-b border-gray-100 pb-3">

                            <button type="button"
                                class="accordion-btn w-full flex justify-between items-center text-left font-medium text-gray-800 hover:text-black transition py-1"
                                data-target="{{ $id }}">

                                <span>{{ $label }}</span>

                                <div class="flex items-center gap-2">
                                    <span class="accordion-check text-green-600 hidden text-sm">✓</span>
                                    <span class="accordion-icon text-sm transition-transform duration-300">˅</span>
                                </div>

                            </button>

                            <div id="{{ $id }}"
                                class="accordion-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">

                                <div class="pt-3 text-gray-600 text-sm leading-relaxed">

                                    @if ($id == 'details')
                                        {!! nl2br(e($product->description ?? 'Описание временно недоступно.')) !!}
                                    @elseif($id == 'care')
                                        Информация по уходу за изделием появится позже.
                                    @elseif($id == 'delivery')
                                        Детали доставки уточняйте при оформлении заказа.
                                    @elseif($id == 'ask')
                                        Вы можете задать вопрос через форму обратной связи.
                                    @endif

                                </div>
                            </div>

                        </div>
                    @endforeach

                </div>

                {{-- FAVORITE + CART --}}
                <div class="mt-6 flex items-center gap-3">

                    @php
                        $isFavorite = auth()->user()?->favorites()?->where('product_id', $product->id)?->exists();
                    @endphp

                    {{-- CART --}}
                    <div class="flex-1">

                        <div class="mt-6 flex items-center gap-3">

                            {{-- CART --}}
                            <form action="{{ route('cart.add', $product->slug) }}" id="cartForm" method="POST"
                                class="flex-1">
                                @csrf

                                <input type="hidden" name="color" id="selectedColor" value="{{ $selectedColorKey }}">
                                <input type="hidden" name="size_id" id="sizeInput">

                                <button type="submit" id="cartBtn"
                                    class="w-full h-16 text-sm transition flex items-center justify-center
    {{ $inCart ? 'bg-white text-black border border-black' : 'bg-black text-white' }}">

                                    <span id="cartText">
                                        {{ $inCart ? 'В корзине' : 'Добавить в корзину' }}
                                    </span>

                                </button>
                            </form>
                            {{-- FAVORITE --}}
                            <form method="POST" action="{{ route('favorites.toggle', $selectedColor->id) }}">
                                @csrf

                                <button type="submit"
                                    class="w-16 h-16 flex items-center justify-center border border-gray-300 rounded-md hover:border-black transition">

                                    <img src="{{ $inFavorite ? asset('favorite1.svg') : asset('favorite.png') }}"
                                        class="w-5 h-5" alt="favorite">

                                </button>
                            </form>

                        </div>



                    </div>
                </div>
            </div>

            <div id="recommendedBlock" class="opacity-0 max-h-0 overflow-hidden transition-all duration-700 ease-in-out">

                <div class="mb-8">
                    <span class="uppercase tracking-[0.25em] text-sm text-gray-500">
                        Complete the look
                    </span>

                    <h2 class="text-4xl font-light mt-2">
                        Покупают вместе
                    </h2>


                </div>

                <div id="recommended-slider" class="flex gap-6 overflow-x-auto pb-4 cursor-grab select-none">

                    @foreach ($recommendedProducts as $item)
                        @php
                            $color = $item->productColors->first();
                            $images = $color?->images ?? collect();

                            $image = $images->where('is_main', true)->first() ?? $images->first();

                            $imageUrl = $image ? Storage::url($image->path) : asset('images/placeholder.jpg');

                            $price = $item->sizes->first()?->pivot->price ?? 0;

                            $productUrl = route('products.show', [
                                'slug' => $item->slug, // ✅ ВОТ ТУТ ИСПРАВЛЕНИЕ
                                'color' => Str::slug($color?->color?->title),
                            ]);
                        @endphp

                        <a href="{{ $productUrl }}" class="shrink-0 w-72">
                            <div class="aspect-[3/4] overflow-hidden rounded-sm bg-gray-100">
                                <img src="{{ $imageUrl }}"
                                    class="w-full h-full object-cover transition duration-500 hover:scale-105">
                            </div>

                            <div class="mt-3">
                                <h3 class="text-lg font-medium truncate">
                                    {{ $item->title }}
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

    </div>
    <style>
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-2px);
            }

            75% {
                transform: translateX(2px);
            }
        }
    </style>

    <div id="toast"
        class="fixed bottom-5 left-1/2 -translate-x-1/2
    bg-black text-white px-4 py-2 rounded-lg shadow-lg
    opacity-0 translate-y-2 pointer-events-none
    transition-all duration-300">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            document.querySelectorAll('.accordion-btn').forEach(button => {

                button.addEventListener('click', () => {

                    const targetId = button.dataset.target;
                    const content = document.getElementById(targetId);

                    if (!content) return;

                    const icon = button.querySelector('.accordion-icon');
                    const check = button.querySelector('.accordion-check');

                    const isOpen = content.style.maxHeight &&
                        content.style.maxHeight !== '0px';

                    // закрыть все остальные
                    document.querySelectorAll('.accordion-content').forEach(item => {
                        item.style.maxHeight = null;
                    });

                    document.querySelectorAll('.accordion-icon').forEach(item => {
                        item.style.transform = 'rotate(0deg)';
                    });

                    document.querySelectorAll('.accordion-check').forEach(item => {
                        item.classList.add('hidden');
                    });

                    // если был закрыт — открыть
                    if (!isOpen) {
                        content.style.maxHeight = content.scrollHeight + 'px';

                        if (icon) {
                            icon.style.transform = 'rotate(180deg)';
                        }

                        if (check) {
                            check.classList.remove('hidden');
                        }
                    }

                });

            });

        });
    </script>

    <script>
        const imagesByColor = @json($imagesByColor);

        let toastTimeout;

        function showToast(message, duration = 2000) {
            const toast = document.getElementById('toast');

            toast.textContent = message;
            toast.classList.remove('opacity-0', 'translate-y-2');
            toast.classList.add('opacity-100');

            clearTimeout(toastTimeout);

            toastTimeout = setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                toast.classList.remove('opacity-100');
            }, duration);
        }

        document.addEventListener('DOMContentLoaded', () => {

            // ================= CART =================
            const form = document.getElementById('cartForm');
            const btn = document.getElementById('cartBtn');
            const text = document.getElementById('cartText');

            function setAddedState() {
                btn.classList.remove('bg-black', 'text-white');
                btn.classList.add('bg-white', 'text-black', 'border', 'border-black');
                text.textContent = 'В корзине';
            }

            function showRecommended() {
                const block = document.getElementById('recommendedBlock');
                if (!block) return;

                block.classList.remove('hidden');
                requestAnimationFrame(() => {
                    block.classList.remove('opacity-0', 'max-h-0');
                    block.classList.add('opacity-100');
                });
            }

            form?.addEventListener('submit', async (e) => {
                e.preventDefault();

                const size = document.getElementById('sizeInput')?.value;

                if (!size) {
                    showToast('Выберите размер');
                    return;
                }

                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        color: document.getElementById('selectedColor').value,
                        size_id: size
                    })
                });

                const json = await res.json();

                if (json.success) {
                    setAddedState();
                    showToast(json.message);
                    setTimeout(showRecommended, 200);
                } else {
                    showToast(json.message || 'Ошибка');
                }
            });

            // ================= SIZE =================
            document.querySelectorAll('.size-option').forEach(btn => {
                btn.addEventListener('click', () => {
                    const input = document.getElementById('sizeInput');
                    if (!input) return;

                    input.value = btn.dataset.id;

                    document.querySelectorAll('.size-option').forEach(b => {
                        b.classList.remove('bg-black', 'text-white');
                    });

                    btn.classList.add('bg-black', 'text-white');
                });
            });

            // ================= COLOR + IMAGE =================
            const colorButtons = document.querySelectorAll('.color-option');
            const colorInput = document.getElementById('selectedColor');

            let currentColor = colorInput?.value;
            let currentImages = imagesByColor[currentColor] || [];
            let currentIndex = 0;

            const img = document.getElementById('productImage');
            const next = document.getElementById('nextImage');
            const prev = document.getElementById('prevImage');

            function updateImage(i) {
                if (!currentImages.length) return;

                img.classList.add('opacity-0');

                setTimeout(() => {
                    img.src = currentImages[i];
                    img.classList.remove('opacity-0');
                }, 150);
            }

            function updateArrows() {
                const has = currentImages.length > 1;
                next.classList.toggle('hidden', !has);
                prev.classList.toggle('hidden', !has);
            }

            next?.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % currentImages.length;
                updateImage(currentIndex);
            });

            prev?.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                updateImage(currentIndex);
            });

            colorButtons.forEach(btn => {
                btn.addEventListener('click', () => {

                    const color = btn.dataset.color;
                    if (colorInput.value === color) return;

                    colorInput.value = color;
                    currentColor = color;
                    currentImages = imagesByColor[color] || [];
                    currentIndex = 0;

                    if (currentImages.length) {
                        updateImage(0);
                    } else {
                        img.src = '/images/placeholder.jpg';
                    }

                    updateArrows();
                });
            });

            updateArrows();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const slider = document.getElementById('recommended-slider');

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
