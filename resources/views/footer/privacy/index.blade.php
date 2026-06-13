@extends('layouts.admin')

@section('title', 'Политика конфиденциальности')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Политика конфиденциальности', 'url' => route('privacy')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')

<div class="container mx-auto py-16">

    {{-- Hero --}}
    <div class="max-w-3xl mb-20">

        <span class="uppercase tracking-[0.35em] text-xs text-gray-400">
            Privacy / Confidentiality
        </span>

        <h1 class="text-5xl font-light mt-5 mb-6 leading-tight">
            Мы бережно относимся к данным
        </h1>

        <p class="text-lg text-gray-600 leading-relaxed">
            Эта политика описывает, как мы собираем, используем и защищаем информацию.
            Мы придерживаемся принципа минимального вмешательства — только то, что действительно нужно для работы сервиса.
        </p>

    </div>

    {{-- Sections --}}
    <div class="max-w-3xl space-y-16">

        <section>
            <h2 class="text-xl font-medium tracking-wide mb-3">
                01 — Сбор данных
            </h2>

            <p class="text-gray-600 leading-relaxed">
                Мы собираем только те данные, которые вы сами предоставляете:
                при регистрации, оформлении заказа или взаимодействии с сайтом.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-medium tracking-wide mb-3">
                02 — Использование
            </h2>

            <p class="text-gray-600 leading-relaxed">
                Информация используется для обработки заказов, улучшения сервиса
                и создания персонального опыта.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-medium tracking-wide mb-3">
                03 — Защита
            </h2>

            <p class="text-gray-600 leading-relaxed">
                Мы применяем современные методы защиты данных и не храним
                платёжную информацию на стороне сервера.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-medium tracking-wide mb-3">
                04 — Передача данных
            </h2>

            <p class="text-gray-600 leading-relaxed">
                Мы не передаём данные третьим лицам, кроме случаев,
                когда это требуется по закону или для выполнения заказа.
            </p>
        </section>

    </div>

    {{-- subtle divider --}}
    <div class="max-w-3xl mt-20 border-t border-gray-200 pt-10">

        <p class="text-sm text-gray-500 leading-relaxed">
            Последнее обновление: {{ date('Y') }}
            <br>
            Мы оставляем за собой право обновлять политику без предварительного уведомления.
        </p>

    </div>

</div>

@endsection
