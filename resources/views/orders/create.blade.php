@extends('layouts.admin')

@section('content')
    <div class="container mx-auto py-10 max-w-5xl">

        {{-- HEADER --}}
        <h1 class="text-2xl font-medium text-gray-800 mb-8">
            Оформление заказа
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT: FORM --}}
            <div class="lg:col-span-2 space-y-6">

                <form action="{{ route('orders.store') }}" method="POST" class="bg-white border rounded-sm p-6 space-y-4">
                    @csrf

                    {{-- ADDRESS --}}
                    <div class="space-y-4">

                        <h2 class="text-sm font-semibold text-gray-700">
                            Адрес доставки
                        </h2>

                        {{-- CITY --}}
                        <div>
                            <label class="text-sm text-gray-600">Город</label>
                            <input type="text" name="city" class="w-full border rounded-sm px-3 py-2 mt-1"
                                placeholder="Например: Москва" required>
                        </div>

                        {{-- STREET --}}
                        <div>
                            <label class="text-sm text-gray-600">Улица</label>
                            <input type="text" name="street" class="w-full border rounded-sm px-3 py-2 mt-1"
                                placeholder="Например: Тверская" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            {{-- HOUSE --}}
                            <div>
                                <label class="text-sm text-gray-600">Дом</label>
                                <input type="text" name="house" class="w-full border rounded-sm px-3 py-2 mt-1"
                                    placeholder="12" required>
                            </div>

                            {{-- APARTMENT --}}
                            <div>
                                <label class="text-sm text-gray-600">Квартира</label>
                                <input type="text" name="apartment" class="w-full border rounded-sm px-3 py-2 mt-1"
                                    placeholder="45">
                            </div>

                        </div>

                        {{-- POSTAL CODE --}}
                        <div>
                            <label class="text-sm text-gray-600">Почтовый индекс</label>
                            <input type="text" name="postal_code" class="w-full border rounded-sm px-3 py-2 mt-1"
                                placeholder="101000" maxlength="10">
                        </div>

                    </div>

                    {{-- PAYMENT --}}
                    <div>
                        <label class="text-sm text-gray-600">Способ оплаты</label>
                        <select name="payment_method" class="w-full border rounded-sm px-3 py-2 mt-1" required>
                            <option value="cash">Наличные</option>
                            <option value="card">Карта</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-gray-900 text-white py-3 rounded-sm hover:bg-gray-800 transition">
                        Подтвердить заказ
                    </button>
                </form>

            </div>

            {{-- RIGHT: SUMMARY --}}
            <div class="bg-white border rounded-sm p-6 h-fit">

                <h2 class="text-lg font-medium mb-4">Ваш заказ</h2>

                <div class="space-y-3">

                    @foreach ($cart->items as $item)
                        <div class="flex justify-between text-sm text-gray-700">
                            <span>
                                {{ $item->product->title }} × {{ $item->quantity }}
                            </span>

                            <span>
                                {{ number_format($item->price * $item->quantity, 0, '', ' ') }} ₽
                            </span>
                        </div>
                    @endforeach

                </div>

                <div class="border-t mt-4 pt-4 flex justify-between font-medium">
                    <span>Итого</span>
                    <span>
                        {{ number_format($cart->items->sum(fn($i) => $i->price * $i->quantity), 0, '', ' ') }} ₽
                    </span>
                </div>

            </div>

        </div>

    </div>
@endsection
