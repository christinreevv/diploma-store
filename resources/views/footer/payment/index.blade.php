@extends('layouts.admin')

@section('title', 'Оплата')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Оплата', 'url' => route('payment')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')

<div class="container mx-auto py-12">

    {{-- Hero --}}
    <div class="max-w-4xl mb-16">
        <span class="uppercase tracking-[0.3em] text-sm text-gray-500">
            Payment
        </span>

        <h1 class="text-5xl font-light mt-4 mb-6">
            Оплата
        </h1>

        <p class="text-lg text-gray-600 leading-relaxed">
            Мы предлагаем удобные и безопасные способы оплаты.
            Все платежи проходят через защищённые платёжные системы,
            обеспечивая безопасность ваших данных.
        </p>
    </div>

    {{-- Способы оплаты --}}
    <section class="mb-20">

        <h2 class="text-3xl font-light mb-8">
            Доступные способы оплаты
        </h2>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="border border-gray-200 p-8">
                <h3 class="text-xl font-medium mb-3">
                    Банковская карта
                </h3>

                <p class="text-gray-600">
                    Visa, MasterCard, МИР и другие популярные карты.
                </p>
            </div>

            <div class="border border-gray-200 p-8">
                <h3 class="text-xl font-medium mb-3">
                    Онлайн-оплата
                </h3>

                <p class="text-gray-600">
                    Мгновенная оплата через защищённый платёжный сервис.
                </p>
            </div>

            <div class="border border-gray-200 p-8">
                <h3 class="text-xl font-medium mb-3">
                    При получении
                </h3>

                <p class="text-gray-600">
                    Оплата наличными или картой курьеру.
                </p>
            </div>

            <div class="border border-gray-200 p-8">
                <h3 class="text-xl font-medium mb-3">
                    Сертификаты
                </h3>

                <p class="text-gray-600">
                    Использование подарочных сертификатов магазина.
                </p>
            </div>

        </div>

    </section>

    {{-- Как проходит оплата --}}
    <section class="mb-20">

        <h2 class="text-3xl font-light mb-10">
            Как проходит оплата
        </h2>

        <div class="grid md:grid-cols-4 gap-6">

            <div class="bg-gray-50 p-6">
                <div class="text-4xl font-light mb-4">01</div>

                <h3 class="font-medium mb-2">
                    Выберите товар
                </h3>

                <p class="text-gray-600">
                    Добавьте понравившиеся товары в корзину.
                </p>
            </div>

            <div class="bg-gray-50 p-6">
                <div class="text-4xl font-light mb-4">02</div>

                <h3 class="font-medium mb-2">
                    Оформите заказ
                </h3>

                <p class="text-gray-600">
                    Укажите данные для доставки.
                </p>
            </div>

            <div class="bg-gray-50 p-6">
                <div class="text-4xl font-light mb-4">03</div>

                <h3 class="font-medium mb-2">
                    Выберите оплату
                </h3>

                <p class="text-gray-600">
                    Подберите удобный способ оплаты.
                </p>
            </div>

            <div class="bg-gray-50 p-6">
                <div class="text-4xl font-light mb-4">04</div>

                <h3 class="font-medium mb-2">
                    Подтверждение
                </h3>

                <p class="text-gray-600">
                    После успешной оплаты заказ поступит в обработку.
                </p>
            </div>

        </div>

    </section>

    {{-- Безопасность --}}
    <section class="mb-20">

        <h2 class="text-3xl font-light mb-10">
            Безопасность платежей
        </h2>

        <div class="grid lg:grid-cols-2 gap-8">

            <div class="bg-gray-50 p-8">
                <h3 class="text-xl font-medium mb-4">
                    Защищённые транзакции
                </h3>

                <p class="text-gray-600 leading-relaxed">
                    Все операции проходят через сертифицированные
                    платёжные шлюзы с использованием современных
                    технологий шифрования данных.
                </p>
            </div>

            <div class="bg-gray-50 p-8">
                <h3 class="text-xl font-medium mb-4">
                    Конфиденциальность
                </h3>

                <p class="text-gray-600 leading-relaxed">
                    Данные банковских карт не сохраняются на сайте
                    и используются исключительно для проведения платежа.
                </p>
            </div>

        </div>

    </section>

    {{-- FAQ --}}
    <section>

        <h2 class="text-3xl font-light mb-10">
            Частые вопросы
        </h2>

        <div class="space-y-4">

            <div class="border border-gray-200 p-6">
                <h3 class="font-medium mb-2">
                    Когда списываются деньги?
                </h3>

                <p class="text-gray-600">
                    Средства списываются сразу после подтверждения платежа.
                </p>
            </div>

            <div class="border border-gray-200 p-6">
                <h3 class="font-medium mb-2">
                    Можно ли оплатить при получении?
                </h3>

                <p class="text-gray-600">
                    Да, если данный способ доступен для выбранного региона доставки.
                </p>
            </div>

            <div class="border border-gray-200 p-6">
                <h3 class="font-medium mb-2">
                    Что делать, если платёж не прошёл?
                </h3>

                <p class="text-gray-600">
                    Проверьте баланс карты или свяжитесь с банком.
                    Если проблема сохраняется — обратитесь в службу поддержки.
                </p>
            </div>

        </div>

    </section>

</div>

@endsection
