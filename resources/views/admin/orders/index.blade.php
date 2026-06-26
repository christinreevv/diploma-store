@extends('layouts.admin')

@section('title', 'Заказы')

@section('breadcrumbs')
    @php
        $breadcrumbs = [['label' => 'Главная', 'url' => url('/')], ['label' => 'Заказы', 'url' => '#']];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')


    <div class="container mx-auto py-10 space-y-8">

        {{-- HEADER --}}
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-light text-gray-700 mb-8">
                Все заказы
            </h1>
        </div>

        {{-- GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            @foreach ($orders as $order)
                @php
                    $total = $order->items->sum(fn($i) => $i->price * $i->quantity);

                    if ($order->user) {
                        // Берём ВСЕ заказы пользователя, сортируем от старого к новому
                        $userOrders = $order->user->orders->sortBy('created_at')->values();

                        // Находим позицию текущего заказа в этой коллекции
                        $orderNumber = $userOrders->search(fn($userOrder) => $userOrder->id === $order->id);

                        // Делаем человеческий номер
                        $orderNumber = $orderNumber !== false ? $orderNumber + 1 : 1;
                    } else {
                        $orderNumber = 1;
                    }
                @endphp

                <div class="border border-gray-200 bg-white hover:border-gray-300 transition">

                    {{-- TOP --}}
                    <div class="p-5 flex items-start justify-between">

                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-400">
                                Заказ #{{ $orderNumber }}
                            </p>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $order->created_at->format('d.m.Y H:i') }}
                            </p>

                            <p class="text-sm text-gray-700 mt-2">
                                {{ $order->user?->name ?? 'Гость' }}
                            </p>
                        </div>

                        {{-- STATUS --}}
                        @php
                            $statuses = [
                                ['key' => 'Новый', 'label' => 'Новый', 'color' => 'bg-gray-400'],
                                ['key' => 'В обработке', 'label' => 'В обработке', 'color' => 'bg-yellow-400'],
                                ['key' => 'Отправлен', 'label' => 'Отправлен', 'color' => 'bg-blue-500'],
                                ['key' => 'Доставлен', 'label' => 'Доставлен', 'color' => 'bg-green-500'],
                                ['key' => 'Отменён', 'label' => 'Отменён', 'color' => 'bg-red-500'],
                            ];
                        @endphp

                        <button type="button" class="flex items-center gap-2 text-xs js-toggle-status"
                            data-id="{{ $order->id }}" data-url="{{ route('admin.orders.status', $order) }}"
                            data-status="{{ $order->status }}">

                            <span class="w-2 h-2 rounded-full status-dot"></span>

                            <span class="status-text"></span>

                        </button>
                    </div>

                    {{-- MIDDLE --}}
                    <div class="px-5 pb-5 space-y-3">

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Товары</span>
                            <span class="text-gray-900 font-medium">{{ $order->items->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Сумма</span>
                            <span class="text-gray-900 font-medium">
                                {{ number_format($total, 0, ',', ' ') }} ₽
                            </span>
                        </div>

                    </div>

                    {{-- BOTTOM --}}
                    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">

                        <a href="{{ route('admin.orders.show', ['order' => $order->id, 'number' => $orderNumber]) }}"
                            class="text-sm text-gray-500 hover:text-black transition">
                            Открыть
                        </a>

                        <span class="text-xs text-gray-400">
                            № {{ $orderNumber }}
                        </span>

                    </div>

                </div>
            @endforeach

        </div>

        {{-- PAGINATION --}}
        <div class="flex justify-center pt-6">
            {{ $orders->links() }}
        </div>

    </div>

    {{-- AJAX STATUS TOGGLE --}}
    <script>
        document.querySelectorAll('.js-toggle-status').forEach(btn => {

            const statuses = [{
                    key: 'Новый',
                    label: 'Новый',
                    color: 'bg-gray-400'
                },
                {
                    key: 'В обработке',
                    label: 'В обработке',
                    color: 'bg-yellow-400'
                },
                {
                    key: 'Отправлен',
                    label: 'Отправлен',
                    color: 'bg-blue-500'
                },
                {
                    key: 'Доставлен',
                    label: 'Доставлен',
                    color: 'bg-green-500'
                },
                {
                    key: 'Отменён',
                    label: 'Отменён',
                    color: 'bg-red-500'
                },
            ];

            function render(status) {
                const dot = btn.querySelector('.status-dot');
                const text = btn.querySelector('.status-text');

                const current = statuses.find(s => s.key === status) || statuses[0];

                dot.className = `w-2 h-2 rounded-full ${current.color}`;
                text.textContent = current.label;
            }

            render(btn.dataset.status);

            btn.addEventListener('click', async function() {

                let currentIndex = statuses.findIndex(s => s.key === btn.dataset.status);
                let nextIndex = (currentIndex + 1) % statuses.length;
                let nextStatus = statuses[nextIndex].key;

                const res = await fetch(btn.dataset.url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        status: nextStatus
                    })
                });

                if (!res.ok) return;

                const data = await res.json();

                btn.dataset.status = data.status;

                render(data.status);
            });

        });
    </script>
@endsection
