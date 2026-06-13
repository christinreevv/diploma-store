@extends('layouts.admin')

@section('title', 'Корзина')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Корзина', 'url' => route('cart.index')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
    <style>
        @layer utilities {

            @layer utilities {

                /* Firefox */
                .no-spinner {
                    -moz-appearance: textfield;
                    appearance: textfield;
                }

                /* Chrome / Safari / Edge */
                .no-spinner::-webkit-outer-spin-button,
                .no-spinner::-webkit-inner-spin-button {
                    -webkit-appearance: none;
                    margin: 0;
                }
            }
        }
    </style>

    <div class="container mx-auto px-4 py-10">


        @if ($cart && $cart->items->count() > 0)

            <div class="flex flex-col lg:flex-row gap-10">

                {{-- LEFT --}}
                <div class="flex-1 space-y-6">

                    @foreach ($cart->items as $item)
                        @php
                            $image = $item->productColor?->images?->first();

                            $stock = $item->productSize?->stock ?? ($item->productSize?->pivot?->stock ?? 999999);
                        @endphp

                        @php
                            $image = $item->productColor?->images?->first();
                        @endphp

                        <div class="flex gap-6 border-b pb-6">

                            {{-- IMAGE --}}
                            <div class="w-28 h-36 rounded overflow-hidden flex items-center justify-center">

                                <a href="{{ route('products.show', $item->product->slug) }}" class="block w-full h-full">
                                    @if ($image)
                                        <img src="{{ Storage::url($image->path) }}" class="object-cover w-full h-full">
                                    @endif
                                </a>

                            </div>

                            {{-- INFO --}}
                            <div class="flex-1">

                                <div class="flex justify-between">

                                    <h2 class="font-medium text-lg">
                                        <a href="{{ route('products.show', $item->product->slug) }}"
                                            class="hover:underline">
                                            {{ $item->product->title }}
                                        </a>
                                    </h2>

                                    {{-- PRICE --}}
                                    <div class="font-semibold text-lg">
                                        <span class="item-price" data-id="{{ $item->id }}"
                                            data-base="{{ $item->price }}">
                                            {{ number_format($item->price * $item->quantity, 0, ',', ' ') }}
                                        </span> ₽
                                    </div>

                                </div>

                                <p class="text-gray-500 text-sm mt-1">

                                    Цвет:
                                    {{ optional(optional($item->productColor)->color)->title ?? '-' }}

                                    <br>

                                    Размер:
                                    {{ optional(optional($item->productSize)->size)->title ?? '-' }}

                                </p>
                                <div class="flex justify-between items-end mt-4">

                                    {{-- QUANTITY --}}
                                    <div
                                        class="flex items-center rounded-md border border-gray-200 overflow-hidden w-fit bg-white shadow-sm">

                                        {{-- MINUS --}}
                                        <button type="button"
                                            class="step-btn w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-50 active:scale-95 transition"
                                            data-action="dec" data-id="{{ $item->id }}">
                                            −
                                        </button>

                                        {{-- INPUT --}}
                                        {{-- INPUT --}}
                                        <input type="number"
                                            class="quantity-input no-spinner w-12 h-10 text-center border-x border-gray-200 outline-none text-sm font-medium"
                                            value="{{ $item->quantity }}" data-id="{{ $item->id }}"
                                            data-stock="{{ $stock }}">

                                        {{-- PLUS --}}
                                        <button type="button"
                                            class="step-btn w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-50 active:scale-95 transition"
                                            data-action="inc" data-id="{{ $item->id }}">
                                            +
                                        </button>

                                    </div>
                                    {{-- DELETE --}}
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button class="text-red-500 text-sm">
                                            Удалить
                                        </button>

                                    </form>

                                </div>


                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- RIGHT --}}
                <div class="lg:w-1/3 bg-gray-50 p-6 rounded-lg h-fit sticky top-24">

                    <h3 class="text-xl font-semibold mb-4">
                        Итого
                    </h3>

                    <div class="flex justify-between mb-4">
                        <span>Сумма</span>

                        <span id="cart-total">
                            {{ number_format($total, 0, ',', ' ') }} ₽
                        </span>
                    </div>

                    <a href="{{ route('orders.create') }}" class="block bg-black text-white py-3 text-center rounded">
                        Оформить заказ
                    </a>

                </div>

            </div>
        @else
            <p class="text-gray-500">Корзина пустая</p>
        @endif

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            function toast(message) {

                const toast = document.createElement('div');

                toast.className =
                    'fixed bottom-5 left-1/2 -translate-x-1/2 bg-black text-white px-4 py-2 rounded-lg shadow-lg z-50';

                toast.innerText = message;

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 2000);
            }

            function updateServer(id, quantity) {

                fetch(`/cart/update/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity: quantity
                    })
                });

            }

            function updateItemPrice(id, quantity) {

                const priceEl = document.querySelector(
                    `.item-price[data-id="${id}"]`
                );

                if (!priceEl) return;

                const basePrice = Number(priceEl.dataset.base);

                priceEl.innerText =
                    new Intl.NumberFormat('ru-RU')
                    .format(basePrice * quantity);
            }

            function updateTotal() {

                let total = 0;

                document.querySelectorAll('.item-price[data-base]')
                    .forEach(priceEl => {

                        total += Number(priceEl.textContent.replace(/\s/g, ''));

                    });

                document.getElementById('cart-total').innerText =
                    new Intl.NumberFormat('ru-RU')
                    .format(total) + ' ₽';
            }

            document.querySelectorAll('.step-btn').forEach(button => {

                button.addEventListener('click', () => {

                    const id = button.dataset.id;

                    const input = document.querySelector(
                        `.quantity-input[data-id="${id}"]`
                    );

                    if (!input) return;

                    let quantity = Number(input.value);

                    const stock = Number(
                        input.dataset.stock || 999999
                    );

                    if (button.dataset.action === 'inc') {

                        if (quantity >= stock) {

                            toast('Больше товара нет на складе');

                            return;
                        }

                        quantity++;

                    } else {

                        quantity = Math.max(1, quantity - 1);

                    }

                    input.value = quantity;

                    updateItemPrice(id, quantity);
                    updateTotal();
                    updateServer(id, quantity);

                });

            });

            document.querySelectorAll('.quantity-input').forEach(input => {

                input.addEventListener('input', () => {

                    let quantity = Number(input.value);

                    const stock = Number(
                        input.dataset.stock || 999999
                    );

                    if (!quantity || quantity < 1) {
                        quantity = 1;
                    }

                    if (quantity > stock) {

                        quantity = stock;

                        toast(`Максимум доступно: ${stock}`);

                    }

                    input.value = quantity;

                    updateItemPrice(
                        input.dataset.id,
                        quantity
                    );

                    updateTotal();

                    updateServer(
                        input.dataset.id,
                        quantity
                    );

                });

            });

            updateTotal();

        });
    </script>
@endsection
