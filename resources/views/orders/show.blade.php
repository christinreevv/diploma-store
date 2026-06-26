@extends('layouts.admin')
@php
    $userOrders = $order->user
        ? $order->user->orders()->orderBy('created_at')->pluck('id')->values()
        : collect();

    $orderNumber = $userOrders->search($order->id) + 1;
@endphp
@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Заказы', 'url' => route('profile.show')],
            ['label' => 'Заказ #' . $orderNumber, 'url' => '#'],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
    <div class="container mx-auto py-10">



        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-medium text-gray-800">
                    Заказ #{{ $orderNumber }}
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Создан: {{ $order->created_at->format('d.m.Y H:i') }}
                </p>
            </div>

            <a href="{{ route('profile.show') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-sm text-sm text-gray-700 hover:bg-gray-50 transition">
                ← Вернуться к списку заказов
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT: ITEMS --}}
            <div class="lg:col-span-2 space-y-4">

                @foreach ($order->items as $item)
                    <div class="bg-white border rounded-sm p-4 flex gap-4">

                        {{-- IMAGE --}}
                        <div class="w-24 h-24 overflow-hidden bg-white rounded-sm flex-shrink-0">

                            @php
                                $imageUrl =
                                    $item->productColor && $item->productColor->images->first()
                                        ? Storage::url($item->productColor->images->first()->path)
                                        : asset('images/placeholder.jpg');
                            @endphp

                            <img src="{{ $imageUrl }}" alt="{{ $item->product->title }}"
                                class="w-full h-full object-cover transition duration-300 group-hover:opacity-70">

                        </div>

                        {{-- INFO --}}
                        <div class="flex-1">

                            <div class="flex justify-between gap-4">
                                <div>
                                    <h2 class="text-lg text-gray-800">
                                        {{ $item->product->title }}
                                    </h2>

                                    <div class="mt-2 space-y-1 text-sm text-gray-500">

                                        @if ($item->productColor && $item->productColor->color)
                                            <p>
                                                Цвет:
                                                {{ $item->productColor->color->title }}
                                            </p>
                                        @endif

                                        @if ($item->productSize)
                                            <p>
                                                Размер:
                                                {{ $item->productSize->size->title }}
                                            </p>
                                        @endif

                                        <p>
                                            Количество: {{ $item->quantity }}
                                        </p>

                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="text-lg text-gray-900 font-medium">
                                        {{ number_format($item->price * $item->quantity, 0, '', ' ') }} ₽
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ number_format($item->price, 0, '', ' ') }} ₽ / шт
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

            {{-- RIGHT: SUMMARY --}}
            <div class="bg-white border rounded-sm p-6 h-fit">

                <h2 class="text-lg font-medium mb-5">
                    Информация о заказе
                </h2>

                <div class="mb-5">
                    @if ($order->payment_status === 'paid')
                        <div class="px-3 py-2 border border-green-200 bg-green-50 text-green-700 text-sm rounded-sm">
                            ✓ Заказ оплачен
                        </div>
                    @elseif ($order->payment_status === 'failed')
                        <div class="px-3 py-2 border border-red-200 bg-red-50 text-red-700 text-sm rounded-sm">
                            ✕ Ошибка оплаты
                        </div>
                    @else
                        <div class="px-3 py-2 border border-amber-200 bg-amber-50 text-amber-700 text-sm rounded-sm">
                            ⏳ Ожидает оплаты
                        </div>
                    @endif
                </div>

                @if ($order->payment_status !== 'paid')
                    @if ($order->payment_status !== 'paid')
                        <a href="{{ route('checkout.payment', $order) }}"
                            class="block w-full text-center bg-gray-900 text-white py-3 rounded-sm hover:bg-gray-800 transition mb-5">
                            Перейти к оплате
                        </a>
                    @endif
                @endif

                <div class="space-y-4 text-sm">

                    <div class="flex justify-between">
                        <span class="text-gray-500">Номер заказа</span>
                        <span class="text-gray-800">#{{ $orderNumber }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Дата</span>
                        <span class="text-gray-800">
                            {{ $order->created_at->format('d.m.Y') }}
                        </span>
                    </div>

                    @if ($order->delivery_address)
                        <div>
                            <p class="text-gray-500 mb-1">
                                Адрес доставки
                            </p>

                            <p class="text-gray-800">
                                {{ $order->delivery_address }}
                            </p>
                        </div>
                    @endif

                    @if ($order->payment_method)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Оплата</span>

                            <span class="text-gray-800">
                                {{ $order->payment_method === 'cash' ? 'Наличные' : 'Карта' }}
                            </span>
                        </div>
                    @endif

                </div>

                <div class="border-t mt-6 pt-6 flex justify-between items-center">
                    <span class="text-lg font-medium">
                        Итого
                    </span>

                    <span class="text-xl font-semibold text-gray-900">
                        {{ number_format($order->items->sum(fn($i) => $i->price * $i->quantity), 0, '', ' ') }} ₽
                    </span>
                </div>

            </div>

        </div>

    </div>
@endsection
