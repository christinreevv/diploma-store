<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Магазин')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ТВОИ СТИЛИ (важно ПОСЛЕ Bootstrap) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Tailwind (если используешь CDN — всегда последним) -->
    <script src="https://cdn.tailwindcss.com"></script>

    @stack('styles')
</head>

<body class="bg-light">

    <header class="bg-white sticky top-0 z-50">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center relative">

            {{-- НАВИГАЦИЯ (ПК) --}}
            <nav class="hidden md:flex space-x-6">

                {{-- ГОСТЬ И ПОЛЬЗОВАТЕЛЬ --}}
                @if (!auth()->check() || auth()->user()->role === 'user')
                    <a href="{{ route('catalog.index') }}"
                        class="text-[16px] tracking-wide hover:text-red-600 transition text-gray-800">
                        Каталог
                    </a>
                @endif

                {{-- АДМИН --}}
                @auth
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.products.index') }}" class="text-[16px] hover:text-red-600 transition">
                            Товары
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="text-[16px] hover:text-red-600 transition">
                            Пользователи
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="text-[16px] hover:text-red-600 transition">
                            Заказы
                        </a>
                    @endif
                @endauth

            </nav>
            <div class="absolute left-1/2 transform -translate-x-1/2">
                <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.products.index') : route('catalog.index') }}"
                    class="logo text-xl uppercase gradient-text">
                    REVERIE
                </a>
            </div>


            {{-- ПРАВАЯ ЧАСТЬ (ПК) --}}
            <div class="hidden md:flex items-center space-x-4 text-sm">

                {{-- ТОЛЬКО ПОЛЬЗОВАТЕЛЬ --}}
                @auth
                    @if (auth()->check() && auth()->user()->role === 'user')
                        <a href="{{ route('favorites.index') }}">
                            <img src="{{ asset('favorite.png') }}" class="w-5 h-5 hover:opacity-80 transition">
                        </a>

                        <a href="{{ route('cart.index') }}">
                            <img src="{{ asset('cart.png') }}" class="w-6 h-6 hover:opacity-80 transition">
                        </a>
                    @endif
                @endauth

                {{-- АВТОРИЗАЦИЯ --}}
                @auth
                    <a href="{{ route('profile.show') }}" class="text-[16px] hover:text-red-600 transition">
                        Профиль
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="text-[16px] hover:text-red-600 transition">
                            Выйти
                        </button>
                    </form>
                @endauth

                {{-- ГОСТЬ --}}
                @guest
                    <a href="{{ route('login') }}" class="text-[16px] hover:text-red-600 transition">Войти</a>
                    <a href="{{ route('register') }}" class=" text-[16px] hover:text-red-600 transition">Регистрация</a>
                @endguest

            </div>

            {{-- МОБИЛЬНОЕ МЕНЮ --}}
            <button id="mobileMenuBtn" class="md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- МОБИЛЬНАЯ НАВИГАЦИЯ --}}
        <div id="mobileMenu" class="md:hidden hidden bg-white border-t">
            <nav class="flex flex-col space-y-2 px-6 py-4 text-sm">

                @if (!auth()->check() || auth()->user()->role === 'user')
                    <a class="u-nav" href="{{ route('catalog.index') }}">Каталог</a>
                    <a class="u-nav" href="{{ route('cart.index') }}">Корзина</a>
                @endif

                @auth
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.products.index') }}">Товары</a>
                        <a href="{{ route('admin.users.index') }}">Пользователи</a>
                        <a href="{{ route('admin.orders.index') }}">Пользователи</a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button>Выйти</button>
                    </form>
                @endauth

                @guest
                    <a href="{{ route('login') }}">Войти</a>
                    <a href="{{ route('register') }}">Регистрация</a>
                @endguest

            </nav>
        </div>

        <script>
            document.getElementById('mobileMenuBtn')
                .addEventListener('click', () =>
                    document.getElementById('mobileMenu').classList.toggle('hidden')
                );
        </script>
    </header>


    @yield('breadcrumbs')


    <main class="container">
        @yield('content')
    </main>

    <footer class="mt-24 border-t border-gray-200">

        <div class="container mx-auto py-16">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

                {{-- Бренд --}}
                <div>
                    <h3 class="text-2xl font-light mb-4">
                        STORE
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        Современный магазин одежды,
                        вдохновлённый минимализмом и качеством.
                    </p>
                </div>

                {{-- Каталог --}}
                <div>
                    <h4 class="font-medium mb-4">
                        Каталог
                    </h4>

                    <ul class="space-y-2 text-gray-600">
                        <li>
                            <a href="{{ route('catalog.index') }}" class="hover:text-black">
                                Все товары
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('collections.limited') }}" class="hover:text-black">
                                Лимитированная коллекция
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('new-arrivals') }}" class="hover:text-black">
                                Новинки
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Покупателям --}}
                <div>
                    <h4 class="font-medium mb-4">
                        Покупателям
                    </h4>

                    <ul class="space-y-2 text-gray-600">

                        <li>
                            <a href="{{ route('delivery') }}" class="hover:text-black">
                                Доставка и возврат
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('payment') }}" class="hover:text-black">
                                Оплата
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('privacy') }}" class="hover:text-black">
                                Политика конфиденциальности
                            </a>
                        </li>

                    </ul>
                </div>

                {{-- Контакты --}}
                <div>
                    <h4 class="font-medium mb-4">
                        Контакты
                    </h4>

                    <ul class="space-y-2 text-gray-600">
                        <li>+7 (999) 123-45-67</li>
                        <li>info@store.ru</li>
                        <li>Москва, Россия</li>
                    </ul>
                </div>

            </div>

            <div
                class="border-t border-gray-200 mt-12 pt-8 flex-col md:flex-row text text-center text-sm text-gray-500">

                <p>
                    © {{ date('Y') }} REVERIE. Все права защищены.
                </p>

            </div>

        </div>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        function setView(size) {

            const grid = document.querySelector('[data-products-grid]');
            if (!grid) return;

            if (size === 1) {
                grid.className = 'grid grid-cols-1 gap-10';
            }

            if (size === 3) {
                grid.className = 'grid grid-cols-1 md:grid-cols-3 gap-8';
            }

            if (size === 5) {
                grid.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setView(3);
        });
    </script>

</body>

</html>
