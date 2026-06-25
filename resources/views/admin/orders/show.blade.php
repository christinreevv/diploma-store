@extends('layouts.admin')

@section('title', 'Заказ #' . $order->id)

@php
    use App\Models\Order;

    $allOrderIds = Order::orderBy('created_at')->pluck('id');
    $orderNumber = $allOrderIds->search($order->id) + 1;
@endphp

@section('content')

    <div class="container mx-auto py-10 space-y-10">

        {{-- HEADER --}}
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-light text-gray-900">
                    Заказ #{{ $orderNumber }}
                </h1>

                <p class="text-gray-500 mt-1">
                    {{ $order->created_at->format('d.m.Y H:i') }}
                </p>
            </div>

            {{-- STATUS BADGE --}}
            <div class="flex items-center gap-2 px-3 py-1 border border-gray-200 text-sm">
                <span
                    class="w-2 h-2 rounded-full
                {{ $order->status === 'completed' ? 'bg-green-500' : 'bg-gray-300' }}">
                </span>

                <span class="text-gray-700">
                    {{ $order->status }}
                </span>
            </div>
        </div>

        {{-- INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="border border-gray-200 p-6 bg-white">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400">
                    Клиент
                </p>

                <p class="mt-3 text-gray-900 font-medium">
                    {{ $order->user?->name ?? 'Гость' }}
                </p>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $order->user?->email ?? '-' }}
                </p>
            </div>

            <div class="border border-gray-200 p-6 bg-white">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400">
                    Сводка
                </p>

                <p class="mt-3 text-sm text-gray-700">
                    Товаров:
                    <span class="font-medium text-black">{{ $order->items->count() }}</span>
                </p>
            </div>

        </div>

        {{-- SECTION TITLE --}}
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-light text-gray-800">
                Товары в заказе
            </h2>


        </div>

        {{-- ITEMS GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            @foreach ($order->items as $item)
                @php
                    $product = $item->product;

                    $image = $product?->images?->where('is_main', true)->first() ?? $product?->images?->first();

                    $imageUrl = $image ? asset('storage/' . $image->path) : asset('images/placeholder.jpg');

                    $total = $item->price * $item->quantity;
                @endphp

                <div class="border border-gray-200 bg-white overflow-hidden group transition hover:border-gray-300">

                    {{-- IMAGE --}}
                    <div class="aspect-[4/5] relative overflow-hidden bg-gray-100">

                        <img src="{{ $imageUrl }}"
                            class="w-full h-full object-cover transition duration-700 group-hover:scale-105 group-hover:brightness-90">

                        {{-- subtle overlay --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition"></div>

                        {{-- product label --}}
                        <div
                            class="absolute bottom-0 left-0 right-0 p-3 opacity-0 group-hover:opacity-100 transition bg-gradient-to-t from-black/40 to-transparent">

                            <p class="text-white text-xs tracking-wide">
                                {{ $product?->title }}
                            </p>

                        </div>

                    </div>

                    {{-- CONTENT --}}
                    <div class="p-5 space-y-4">

                        <h3 class="text-sm font-medium text-gray-900 truncate">
                            {{ $product?->title }}
                        </h3>

                        <div class="space-y-2 text-sm">

                            <div class="flex justify-between">
                                <span class="text-gray-500">Цена</span>
                                <span class="text-gray-900 font-medium">
                                    {{ number_format($item->price, 0, ',', ' ') }} ₽
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Кол-во</span>
                                <span class="text-gray-900 font-medium">
                                    {{ $item->quantity }}
                                </span>
                            </div>

                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="text-gray-500">Итого</span>
                                <span class="text-black font-semibold">
                                    {{ number_format($total, 0, ',', ' ') }} ₽
                                </span>
                            </div>

                        </div>

                    </div>

                </div>
            @endforeach

        </div>

    </div>

@endsection
