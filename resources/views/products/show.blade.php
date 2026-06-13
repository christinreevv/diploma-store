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

        // Формируем массив изображений по цветам
        foreach ($product->productColors as $productColor) {
            $colorKey = $productColor->color->code; // <-- заменили key на code
            $colorImages = $productColor->images->map(fn($img) => Storage::url($img->path))->toArray();
            $imagesByColor[$colorKey] = $colorImages;
        }

        // Общие изображения продукта
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

        $selectedColorKey = $product->productColors->first()?->color->code ?? null;

        // Начальное изображение
        $initialImage =
            $selectedColorKey && isset($imagesByColor[$selectedColorKey])
                ? $imagesByColor[$selectedColorKey][0]
                : $generalImages[0] ?? asset('images/placeholder.jpg');

    @endphp
    @php
        $selectedColor = $product->productColors->first();

        $inFavorite =
            auth()->check() &&
            $selectedColor &&
            auth()->user()->favorites()->where('product_color_id', $selectedColor->id)->exists();
    @endphp

    <div class="flex flex-col min-h-[calc(100vh-120px)]">
        <div class="container mx-auto px-4 py-10 flex-1 grid grid-cols-1 md:grid-cols-2 gap-12">

            {{-- Галерея --}}
            <div id="imageWrapper"
                class="relative flex items-center justify-center rounded-lg p-4 bg-gray-50 overflow-hidden">

                <button id="prevImage" class="absolute left-3 z-10 hover:bg-gray-200 p-2 rounded-md transition">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <img id="productImage" src="{{ $initialImage }}"
                    class="object-contain w-full max-h-[80vh] transition-all duration-300">

                <!-- ZOOM BOX -->
                <div id="zoomBox"
                    class="absolute w-44 h-44 border shadow-xl rounded-lg overflow-hidden bg-white pointer-events-none
            opacity-0 scale-90 transition-all duration-150">
                    <img id="zoomImage" class="absolute w-full h-full object-cover will-change-transform" />
                </div>

                <button id="nextImage" class="absolute right-3 z-10 hover:bg-gray-200 p-2 rounded-md transition">
                    <i class="fa-solid fa-chevron-right"></i>
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
                                    data-color="{{ $color->code }}">
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

                        <form id="cartForm">
                            @csrf

                            <input type="hidden" name="color" id="selectedColor" value="{{ $selectedColorKey }}">
                            <input type="hidden" name="size_id" id="sizeInput">
                            <input type="hidden" name="quantity" id="quantityInput" value="1">

                            <div class="flex gap-2">

                                <button type="button" id="minusBtn" class="w-10 h-10 border rounded-md hidden">
                                    -
                                </button>

                                <button type="submit" id="cartBtn"
                                    class="flex-1 bg-black text-white rounded-sm h-16 text-sm hover:bg-gray-900 transition">
                                    Добавить в корзину
                                </button>

                                <button type="button" id="plusBtn" class="w-10 h-10 border rounded-md hidden">
                                    +
                                </button>

                            </div>
                        </form>

                    </div>

                    {{-- FAVORITE --}}
                    <form method="POST" action="{{ route('favorites.toggle', $selectedColor->id) }}">
                        @csrf

                        <button type="submit" onclick="event.stopPropagation()"
                            class="w-16 h-16 flex items-center justify-center border border-gray-300 rounded-md hover:border-black transition">

                            <img src="{{ $inFavorite ? asset('favorite1.svg') : asset('favorite.png') }}" class="w-5 h-5"
                                alt="favorite">
                        </button>
                    </form>
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
        class="fixed bottom-5 left-1/2 -translate-x-1/2 bg-black text-white px-4 py-2 rounded-lg shadow-lg opacity-0 pointer-events-none transition-all duration-300">
        Фото для этого цвета отсутствует
    </div>

    <script>
        const imagesByColor = @json($imagesByColor);

        function showToast(message) {
            const toast = document.getElementById('toast');

            toast.textContent = message;
            toast.classList.remove('opacity-0', 'translate-y-2');

            toast.classList.add('opacity-100');

            setTimeout(() => {
                toast.classList.add('opacity-0');
            }, 2000);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const form = document.getElementById('cartForm');

            const nextImage = document.getElementById('nextImage');
            const prevImage = document.getElementById('prevImage');

            const sizeButtons = document.querySelectorAll('.size-option');
            const colorButtons = document.querySelectorAll('.color-option');

            const sizeInput = document.getElementById('sizeInput');
            const colorInput = document.getElementById('selectedColor');
            const quantityInput = document.getElementById('quantityInput');

            const cartBtn = document.getElementById('cartBtn');
            const plusBtn = document.getElementById('plusBtn');
            const minusBtn = document.getElementById('minusBtn');

            const productImage = document.getElementById('productImage');

            let currentColor = colorInput.value;
            let currentImages = imagesByColor[currentColor] || [];
            let currentImageIndex = 0;

            let quantity = 1;

            // ------------------------------ size ------------------------------

            sizeButtons.forEach(btn => {

                btn.addEventListener('click', () => {

                    sizeButtons.forEach(b => {
                        b.classList.remove(
                            'bg-black',
                            'text-white',
                            'border-black',
                            'border-red-500',
                            'ring-2',
                            'ring-red-300',
                            'animate-[shake_0.35s_ease-in-out]'
                        );
                    });

                    btn.classList.add(
                        'bg-black',
                        'text-white',
                        'border-black'
                    );

                    sizeInput.value = btn.dataset.id;
                });
            });

            // ------------------------------ color ------------------------------
            colorButtons.forEach(btn => {

                btn.addEventListener('click', () => {

                    // ❗ если цвет уже выбран — ничего не делаем
                    if (colorInput.value === btn.dataset.color) return;

                    // если цвет заблокирован — тоже ничего не делаем
                    if (btn.classList.contains('pointer-events-none')) return;

                    const selectedColor = btn.dataset.color;

                    colorInput.value = selectedColor;

                    currentColor = selectedColor;
                    currentImages = imagesByColor[selectedColor] || [];
                    currentImageIndex = 0;

                    if (currentImages.length) {

                        productImage.classList.add('opacity-0');

                        setTimeout(() => {
                            productImage.src = currentImages[0];
                            productImage.classList.remove('opacity-0');
                        }, 150);
                    }
                });

            });

            // NEXT IMAGE
            nextImage.addEventListener('click', () => {

                if (!currentImages.length) return;

                currentImageIndex++;

                if (currentImageIndex >= currentImages.length) {
                    currentImageIndex = 0;
                }

                productImage.classList.add('opacity-0');

                setTimeout(() => {
                    productImage.src = currentImages[currentImageIndex];
                    productImage.classList.remove('opacity-0');
                }, 150);
            });

            // PREV IMAGE
            prevImage.addEventListener('click', () => {

                if (!currentImages.length) return;

                currentImageIndex--;

                if (currentImageIndex < 0) {
                    currentImageIndex = currentImages.length - 1;
                }

                productImage.classList.add('opacity-0');

                setTimeout(() => {
                    productImage.src = currentImages[currentImageIndex];
                    productImage.classList.remove('opacity-0');
                }, 150);
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                if (!sizeInput.value) {
                    sizeButtons.forEach(btn => {
                        btn.classList.add(
                            'border-red-500',
                            'ring-2',
                            'ring-red-300',
                            'animate-[shake_0.35s_ease-in-out]'
                        );
                    });

                    return;
                }

                const response = await fetch("{{ route('cart.add', $product->slug) }}", {
                    method: 'POST',
                    credentials: 'same-origin', // 🔥 ВОТ ЭТО НЕ ХВАТАЕТ
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (!data.success) {
                    alert('Ошибка добавления в корзину');
                    return;
                }

                // ✅ переключаем кнопку в режим "в корзине"
                cartBtn.classList.remove('bg-black', 'text-white');
                cartBtn.classList.add('bg-white', 'text-black', 'border');

                cartBtn.innerHTML = `
        <div class="flex items-center justify-center gap-3">
            <button type="button" id="minusInline" class="w-8 h-8 border rounded">-</button>
            <span class="text-sm">В корзине</span>
            <button type="button" id="plusInline" class="w-8 h-8 border rounded">+</button>
        </div>
    `;

                plusBtn.classList.add('hidden');
                minusBtn.classList.add('hidden');

                let qty = 1;

                const minusInline = document.getElementById('minusInline');
                const plusInline = document.getElementById('plusInline');

                minusInline.addEventListener('click', async (e) => {
                    e.stopPropagation();

                    if (qty <= 1) return;

                    qty--;
                    quantityInput.value = qty;

                    await updateCart();
                });

                plusInline.addEventListener('click', async (e) => {
                    e.stopPropagation();

                    qty++;
                    quantityInput.value = qty;

                    await updateCart();
                });

                async function updateCart() {
                    await fetch("{{ route('cart.add', $product->slug) }}", {
                        method: 'POST',
                        credentials: 'same-origin', // 🔥 ВОТ ЭТО НЕ ХВАТАЕТ
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });
                }
            });

            // PLUS
            plusBtn.addEventListener('click', () => {

                quantity++;

                quantityInput.value = quantity;
                cartBtn.textContent = quantity;

                updateQuantity();
            });

            // MINUS
            minusBtn.addEventListener('click', () => {

                if (quantity <= 1) return;

                quantity--;

                quantityInput.value = quantity;
                cartBtn.textContent = quantity;

                updateQuantity();
            });

            async function updateQuantity() {

                await fetch(
                    "{{ route('cart.add', $product->slug) }}", {
                        method: 'POST',
                        credentials: 'same-origin', // 🔥 ВОТ ЭТО НЕ ХВАТАЕТ
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });
            }
        });

        document.querySelectorAll('.accordion-btn').forEach(button => {

            button.addEventListener('click', () => {

                const targetId = button.dataset.target;
                const content = document.getElementById(targetId);

                const icon = button.querySelector('.accordion-icon');
                const isOpen = content.style.maxHeight;

                // закрыть все
                document.querySelectorAll('.accordion-content').forEach(el => {
                    el.style.maxHeight = null;
                });

                document.querySelectorAll('.accordion-icon').forEach(i => {
                    i.textContent = '˅';
                });

                // открыть текущий
                if (!isOpen) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    icon.textContent = '˄';
                }
            });

        });
    </script>



@endsection
