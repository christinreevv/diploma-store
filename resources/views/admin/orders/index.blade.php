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
                    <div class="flex items-center gap-2">
    <span class="status-dot w-2 h-2 rounded-full
        @switch($order->status)
            @case('new') bg-gray-400 @break
            @case('processing') bg-yellow-400 @break
            @case('shipped') bg-blue-500 @break
            @case('completed') bg-green-500 @break
            @case('cancelled') bg-red-500 @break
        @endswitch">
    </span>

    <select
        class="js-order-status border border-gray-200 rounded px-2 py-1 text-sm"
        data-url="{{ route('admin.orders.status', $order) }}">

        <option value="new" {{ $order->status == 'new' ? 'selected' : '' }}>
            Новый
        </option>

        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
            В обработке
        </option>

        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>
            Отправлен
        </option>

        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
            Выполнен
        </option>

        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
            Отменён
        </option>
    </select>
</div>
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
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.js-order-status').forEach(select => {

        select.addEventListener('change', async function () {

            const res = await fetch(this.dataset.url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: this.value
                })
            });

            if (!res.ok) {
                alert('Ошибка изменения статуса');
                return;
            }

            const data = await res.json();

            const dot = this.parentElement.querySelector('.status-dot');

            dot.className = 'status-dot w-2 h-2 rounded-full';

            switch (data.status) {
                case 'new':
                    dot.classList.add('bg-gray-400');
                    break;
                case 'processing':
                    dot.classList.add('bg-yellow-400');
                    break;
                case 'shipped':
                    dot.classList.add('bg-blue-500');
                    break;
                case 'completed':
                    dot.classList.add('bg-green-500');
                    break;
                case 'cancelled':
                    dot.classList.add('bg-red-500');
                    break;
            }
        });

    });

});
</script>

@endsection
