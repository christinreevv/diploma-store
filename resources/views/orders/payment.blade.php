@extends('layouts.admin')

@section('content')
    <div class="container mx-auto max-w-md py-16">

        <div class="bg-white border rounded-sm p-8">

            <h1 class="text-xl font-medium mb-6">
                Оплата заказа #{{ $order->id }}
            </h1>

            <div class="mb-6">
                <p class="text-sm text-gray-500">
                    Сумма к оплате
                </p>

                <p class="text-3xl font-light mt-2">
                    {{ number_format($order->total_price, 0, '', ' ') }} ₽
                </p>
            </div>

            {{-- Тестовая карта --}}
            <div class="space-y-4">

                <div>
                    <label class="text-sm text-gray-500">
                        Номер карты
                    </label>

                    <input type="text" value="5555 5555 5555 4444" readonly
                        class="w-full border px-3 py-2 mt-1 bg-gray-50">
                </div>

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="text-sm text-gray-500">
                            Срок
                        </label>

                        <input type="text" value="12/30" readonly class="w-full border px-3 py-2 mt-1 bg-gray-50">
                    </div>

                    <div>
                        <label class="text-sm text-gray-500">
                            CVV
                        </label>

                        <input type="text" value="123" readonly class="w-full border px-3 py-2 mt-1 bg-gray-50">
                    </div>

                </div>

            </div>

            <form id="payment-form" action="{{ route('checkout.fake-pay') }}" method="POST" class="mt-8">
                @csrf

                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <button id="pay-button" type="submit"
                    class="w-full bg-gray-900 text-white py-3 rounded-sm hover:bg-gray-800 transition">
                    Оплатить
                </button>

                <div id="payment-loader" class="hidden mt-4 text-center">
                    <div class="inline-block w-5 h-5 border-2 border-gray-300 border-t-gray-900 rounded-full animate-spin">
                    </div>

                    <p id="payment-status" class="mt-3 text-sm text-gray-500">
                        Проверяем данные карты...
                    </p>
                </div>
            </form>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const form = document.getElementById('payment-form');
            const button = document.getElementById('pay-button');
            const loader = document.getElementById('payment-loader');
            const status = document.getElementById('payment-status');

            form.addEventListener('submit', function(e) {

                e.preventDefault();

                button.disabled = true;
                button.classList.add('opacity-70');
                button.textContent = 'Обработка...';

                loader.classList.remove('hidden');

                const steps = [
                    'Проверяем данные карты...',
                    'Связываемся с банком...',
                    'Подтверждаем платёж...',
                    'Завершаем операцию...'
                ];

                let index = 0;

                const interval = setInterval(() => {
                    index++;

                    if (index < steps.length) {
                        status.textContent = steps[index];
                    }
                }, 800);

                setTimeout(() => {
                    clearInterval(interval);
                    form.submit();
                }, 3200);

            });

        });
    </script>
@endsection
