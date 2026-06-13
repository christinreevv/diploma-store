@extends('layouts.admin')

@section('title', 'Доставка и возврат')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Доставка и возврат', 'url' => route('delivery')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')

<div class="container mx-auto py-12">

    {{-- Hero --}}
    <div class="max-w-4xl mb-16">
        <span class="uppercase tracking-[0.3em] text-sm text-gray-500">
            Delivery & Returns
        </span>

        <h1 class="text-5xl font-light mt-4 mb-6">
            Доставка и возврат
        </h1>

        <p class="text-lg text-gray-600 leading-relaxed">
            Мы стремимся сделать процесс покупки максимально удобным.
            Выберите подходящий способ доставки и получайте заказы быстро и безопасно.
        </p>
    </div>

    {{-- Способы доставки --}}
    <section class="mb-20">

        <h2 class="text-3xl font-light mb-8">
            Способы доставки
        </h2>

        <div class="overflow-hidden rounded-sm border border-gray-200">

            <table class="w-full">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-5 font-medium">Вид доставки</th>
                        <th class="text-left p-5 font-medium">Сроки</th>
                        <th class="text-left p-5 font-medium">Стоимость</th>
                    </tr>
                </thead>

                <tbody>

                    <tr class="border-t">
                        <td class="p-5">
                            Пункт выдачи заказа, постаматы и партнёрские пункты
                        </td>
                        <td class="p-5">
                            От 2 дней
                        </td>
                        <td class="p-5">
                            Бесплатно при заказе от 3 000 ₽
                        </td>
                    </tr>

                    <tr class="border-t">
                        <td class="p-5">
                            Курьерская доставка
                        </td>
                        <td class="p-5">
                            От 2 дней
                        </td>
                        <td class="p-5">
                            Бесплатно при заказе от 3 000 ₽
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <p class="text-sm text-gray-500 mt-4">
            * Для отдельных категорий товаров доставка может быть бесплатной независимо от суммы заказа.
        </p>

    </section>

    {{-- Как оформить заказ --}}
    <section class="mb-20">

        <h2 class="text-3xl font-light mb-10">
            Как оформить заказ
        </h2>

        <div class="grid md:grid-cols-4 gap-6">

            <div class="border border-gray-200 p-6">
                <div class="text-4xl font-light mb-4">01</div>
                <h3 class="font-medium mb-2">Оформите заказ</h3>
                <p class="text-gray-600">
                    Через сайт или мобильное приложение.
                </p>
            </div>

            <div class="border border-gray-200 p-6">
                <div class="text-4xl font-light mb-4">02</div>
                <h3 class="font-medium mb-2">Подтверждение</h3>
                <p class="text-gray-600">
                    Получите письмо на электронную почту.
                </p>
            </div>

            <div class="border border-gray-200 p-6">
                <div class="text-4xl font-light mb-4">03</div>
                <h3 class="font-medium mb-2">Звонок курьера</h3>
                <p class="text-gray-600">
                    За час до доставки курьер свяжется с вами.
                </p>
            </div>

            <div class="border border-gray-200 p-6">
                <div class="text-4xl font-light mb-4">04</div>
                <h3 class="font-medium mb-2">Получение заказа</h3>
                <p class="text-gray-600">
                    Неподошедшие товары можно вернуть сразу.
                </p>
            </div>

        </div>

    </section>

    {{-- Дополнительная информация --}}
    <section class="mb-20">

        <h2 class="text-3xl font-light mb-10">
            Дополнительная информация
        </h2>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="bg-gray-50 p-8">
                <h3 class="text-xl font-medium mb-4">
                    Интервалы доставки
                </h3>

                <ul class="space-y-2 text-gray-600">
                    <li>09:00 – 12:00</li>
                    <li>11:00 – 14:00</li>
                    <li>13:00 – 16:00</li>
                    <li>15:00 – 18:00</li>
                    <li>18:00 – 21:00</li>
                </ul>

                <p class="text-sm text-gray-500 mt-4">
                    Точный интервал доступен при оформлении заказа.
                </p>
            </div>

            <div class="bg-gray-50 p-8">
                <h3 class="text-xl font-medium mb-4">
                    Способы оплаты
                </h3>

                <ul class="space-y-2 text-gray-600">
                    <li>Банковская карта</li>
                    <li>Онлайн-оплата</li>
                    <li>Подарочный сертификат</li>
                    <li>Наличными курьеру</li>
                    <li>Картой при получении</li>
                </ul>
            </div>

            <div class="bg-gray-50 p-8">
                <h3 class="text-xl font-medium mb-4">
                    Возврат товара
                </h3>

                <p class="text-gray-600">
                    Вы всегда можете оформить возврат товара в соответствии
                    с действующим законодательством и правилами магазина.
                </p>

                <a href="#"
                    class="inline-block mt-4 text-black hover:underline">
                    Подробнее о возврате →
                </a>
            </div>

        </div>

    </section>

    {{-- FAQ --}}
    <section>

        <h2 class="text-3xl font-light mb-10">
            Вопросы и ответы
        </h2>

        <div class="space-y-4">

            <div class="border border-gray-200 p-6">
                <h3 class="font-medium mb-2">
                    Сколько занимает доставка?
                </h3>

                <p class="text-gray-600">
                    В среднем от 2 рабочих дней в зависимости от региона.
                </p>
            </div>

            <div class="border border-gray-200 p-6">
                <h3 class="font-medium mb-2">
                    Можно ли оплатить при получении?
                </h3>

                <p class="text-gray-600">
                    Да, доступна оплата картой или наличными курьеру.
                </p>
            </div>

            <div class="border border-gray-200 p-6">
                <h3 class="font-medium mb-2">
                    Как оформить возврат?
                </h3>

                <p class="text-gray-600">
                    Обратитесь в поддержку или оформите заявку через личный кабинет.
                </p>
            </div>

        </div>

    </section>

</div>

@endsection
