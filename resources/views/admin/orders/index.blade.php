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
        $userOrderIds = $order->user->orders()
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->values();

        $orderNumber = $userOrderIds->search($order->id) + 1;
    } else {
        $orderNumber = 1;
    }
@endphp

                <div class="border border-gray-200 bg-white hover:border-gray-300 transition">

                    {{-- TOP --}}
                    <div class="p-5 flex items-start justify-between">

                        @php
                            $orderNumber = $order->user
                                ? $order->user->orders()->where('created_at', '<=', $order->created_at)->count()
                                : $order->id;
                        @endphp

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
                        <button class="js-toggle-order-status flex items-center gap-2 text-xs"
                            data-url="{{ route('admin.orders.toggle-status', $order) }}">

                            <span
                                class="status-dot w-2 h-2 rounded-full
                            {{ $order->status === 'completed' ? 'bg-green-500' : 'bg-gray-300' }}">
                            </span>

                            <span class="status-text text-gray-700">
                                {{ $order->status }}
                            </span>

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

                        <a href="{{ route('admin.orders.show', ['order' => $order->id, 'number' => $orders->firstItem() + $loop->index]) }}"
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
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.js-toggle-order-status').forEach(btn => {

                btn.addEventListener('click', async function() {

                    const res = await fetch(this.dataset.url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'PATCH'
                        })
                    });

                    if (!res.ok) return;

                    const data = await res.json();

                    const dot = this.querySelector('.status-dot');
                    const text = this.querySelector('.status-text');

                    if (data.status === 'completed') {
                        dot.classList.remove('bg-gray-300');
                        dot.classList.add('bg-green-500');
                    } else {
                        dot.classList.remove('bg-green-500');
                        dot.classList.add('bg-gray-300');
                    }

                    text.textContent = data.status;

                });

            });

        });
    </script>

@endsection
