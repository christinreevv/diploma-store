@extends('layouts.admin')

@php
    use App\Models\Order;

    // Заказы пользователя (уже отсортированные)
    $sortedOrders = $user->orders->sortByDesc('created_at')->values();

    // Все заказы системы в правильном порядке (для глобального номера)
    $allOrderIds = Order::orderBy('created_at')->pluck('id')->values();
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

<div class="container mx-auto mt-10 p-6 flex flex-col md:flex-row items-start md:items-center md:space-x-12 space-y-6 md:space-y-0">

    {{-- Аватар --}}
    <div class="flex-shrink-0">
        @if ($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-32 h-32 rounded-full object-cover">
        @else
            <div class="w-32 h-32 bg-gray-200 flex items-center justify-center text-gray-500 text-4xl font-semibold rounded-full">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
    </div>

    {{-- Информация --}}
    <div class="flex-1">
        <div class="flex flex-col sm:flex-row sm:space-x-12 space-y-4 sm:space-y-0 text-gray-700">

            <div>
                <p class="text-sm text-gray-500">Имя</p>
                <p class="text-lg font-medium">{{ $user->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="text-lg font-medium">{{ $user->email }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Роль</p>
                <p class="text-lg font-medium">{{ $user->role }}</p>
            </div>

        </div>
    </div>

</div>

{{-- ЗАКАЗЫ --}}
<div class="container mx-auto p-6 mt-12">

    <h2 class="text-2xl font-semibold mb-6">Заказы пользователя</h2>

    @if ($sortedOrders->count())

        <div class="space-y-4">

            @foreach ($sortedOrders as $order)

                @php
                    // 🔥 ГЛОБАЛЬНЫЙ НОМЕР ЗАКАЗА (с 1)
                    $orderNumber = $allOrderIds->search($order->id) + 1;
                @endphp

                <div class="bg-white shadow rounded-lg p-4 flex justify-between items-center">

                    <div>
                        <p class="font-medium">
                            Заказ #{{ $orderNumber }}
                        </p>

                        <p class="text-sm text-gray-500">
                            {{ $order->created_at->format('d.m.Y H:i') }}
                        </p>

                        <p class="text-sm text-gray-500">
                            Статус: {{ $order->status }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="font-medium text-lg">
                            {{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', ' ') }} ₽
                        </p>

                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="text-sm text-gray-500 hover:text-black">
                            Просмотр
                        </a>
                    </div>

                </div>

            @endforeach

        </div>

    @else
        <p class="text-gray-500">У пользователя пока нет заказов.</p>
    @endif

</div>

@endsection
