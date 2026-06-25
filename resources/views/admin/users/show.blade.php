@extends('layouts.admin')

@php
    $sortedOrders = $user->orders->sortByDesc('created_at')->values();
@endphp

@section('title', $user->name)

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Пользователи', 'url' => route('admin.users.index')],
            ['label' => $user->name, 'url' => '#'],
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

            <div class="flex flex-col sm:flex-row sm:space-x-12 space-y-4 sm:space-y-0 text-gray-700">

                <div class="space-y-1">
                    <p class="text-sm font-normal text-gray-500">
                        Имя
                    </p>

                    <p class="text-lg font-medium">
                        {{ $user->name }}
                    </p>
                </div>

                <div class="space-y-1">
                    <p class="text-sm font-normal text-gray-500">
                        Email
                    </p>

                    <p class="text-lg font-medium">
                        {{ $user->email }}
                    </p>
                </div>

                <div class="space-y-1">
                    <p class="text-sm font-normal text-gray-500">
                        Роль
                    </p>

                    <p class="text-lg font-medium">
                        {{ $user->role }}
                    </p>
                </div>

            </div>

        </div>

    </div>

    {{-- Блок заказов --}}
    <div class="container p-6 mx-auto mt-12">

        <h2 class="text-2xl font-semibold mb-6">
            Заказы пользователя
        </h2>

        @if ($user->orders->count())

            <div class="space-y-4">

                @foreach ($sortedOrders as $order)
                    <div
                        class="bg-white shadow rounded-lg p-4 flex flex-col md:flex-row justify-between items-start md:items-center space-y-2 md:space-y-0">

                        <div class="flex-1">

                            <p class="font-medium">
                                Заказ #{{ $loop->iteration }}
                            </p>

                            <p class="text-sm text-gray-500">
                                Дата: {{ $order->created_at->format('d.m.Y H:i') }}
                            </p>

                            <p class="text-sm text-gray-500">
                                Статус: {{ $order->status }}
                            </p>

                        </div>

                        <div class="flex items-center space-x-4">

                            <p class="font-medium text-lg">

                                {{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', ' ') }}

                                ₽

                            </p>

                            <a href="{{ route('admin.users.show', [
                                'user' => $u->id,
                                'number' => $users->firstItem() + $loop->index,
                            ]) }}"
                                class="text-sm text-gray-500 hover:text-black transition">
                                Открыть
                            </a>

                        </div>

                    </div>
                @endforeach

            </div>
        @else
            <p class="text-gray-500">
                У пользователя пока нет заказов.
            </p>

        @endif

    </div>

@endsection
