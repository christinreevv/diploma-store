@extends('layouts.admin')

@section('title', 'Мой профиль')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Профиль', 'url' => route('profile.show')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
    <div
        class="container mx-auto mt-10 p-6 flex flex-col md:flex-row items-start md:items-center md:space-x-12 space-y-6 md:space-y-0">

        {{-- Аватар --}}
        <div class="flex-shrink-0">
            @if ($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                    class="w-32 h-32 rounded-full object-cover">
            @else
                <div
                    class="w-32 h-32 bg-gray-200 flex items-center justify-center text-gray-500 text-4xl font-semibold rounded-full">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>

        {{-- Информация пользователя --}}
        <div class="flex-1 relative w-full">
            {{-- Кнопка настроек --}}
            <div class="absolute top-0 right-0 mt-0 md:mt-2 mr-0 md:mr-2">
                <form action="{{ route('profile.edit') }}" method="GET">
                    <button type="submit" class="text-white p-2 rounded-sm transition flex items-center justify-center">
                        <img src="{{ asset('setting.png') }}" alt="Настройки" class="w-6 h-6">
                    </button>
                </form>
            </div>

            {{-- Имя и Email --}}
            <div class="flex flex-col sm:flex-row sm:space-x-12 space-y-4 sm:space-y-0 text-gray-700 mt-12 md:mt-0">
                <div class="space-y-1">
                    <p class="text-sm font-normal text-gray-500">Имя</p>
                    <p class="text-lg font-medium">{{ $user->name }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-normal text-gray-500">Email</p>
                    <p class="text-lg font-medium">{{ $user->email }}</p>
                </div>
            </div>

            @if (auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.products.index') }}"
                    class="text-black hover:underline font-medium mt-6 inline-block">
                    Перейти в админ-панель
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="max-w-6xl mx-auto bg-green-50 text-green-800 p-4 mt-4 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    {{-- Блок заказов --}}
    <div class="container p-6 mx-auto mt-12">
        <h2 class="text-2xl font-semibold mb-6">Мои заказы</h2>

        @if ($user->orders->count())
            <div class="space-y-4">
                @foreach ($user->orders->sortByDesc('created_at') as $order)
                    <div
                        class="bg-white shadow rounded-lg p-4 flex flex-col md:flex-row justify-between items-start md:items-center space-y-2 md:space-y-0">
                        <div class="flex-1">
                            <p class="font-medium">Заказ #{{ $loop->iteration }}</p>
                            <p class="text-sm text-gray-500">Дата: {{ $order->created_at->format('d.m.Y H:i') }}</p>
                            <p class="text-sm text-gray-500">
                                Статус: {{ $order->status }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-4 mt-2 md:mt-0">
                            <p class="font-medium text-lg">
                                {{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', ' ') }}
                                ₽
                            </p>
                            <a href="{{ route('orders.show', ['order' => $order->id, 'number' => $loop->iteration]) }}"
                                class="text-blue-600 hover:underline text-sm">
                                Просмотр
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center text-center py-20 px-6">

                {{-- ICON --}}
                <div class="w-20 h-20 mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m14-9l2 9M9 22h6" />
                    </svg>
                </div>

                {{-- TITLE --}}
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">
                    У вас пока нет заказов
                </h2>

                {{-- TEXT --}}
                <p class="text-gray-500 max-w-md mb-6">
                    Вы ещё не оформляли заказы.
                </p>

                {{-- CTA --}}
                <a href="{{ route('cart.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white rounded-sm hover:bg-gray-800 transition">

                    <img src="{{ asset('cart.png') }}" class="w-5 h-5" alt="cart">

                    Перейти в корзину
                </a>

            </div>
        @endif
    </div>
@endsection
