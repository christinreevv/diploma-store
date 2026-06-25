@extends('layouts.admin')

@section('title', 'Вход')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-90 to-gray-200 p-4">
        <div class="w-full max-w-md backdrop-blur p-8">
            <h1 class="text-3xl font-medium mb-6 text-center text-gray-800">Вход в аккаунт</h1>

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6 w-full max-w-md mx-auto">
                @csrf

                {{-- ======== EMAIL ======== --}}
                <div>
                    <label for="email" class="block mb-1 font-medium">Введите e-mail:</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full p-3 border border-gray-300 rounded-sm focus:border-black outline-none transition" placeholder="ekaterinasokolova@example.ru">
                    @error('email')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ======== ПАРОЛЬ ======== --}}
                <div>
                    <label for="password" class="block mb-1 font-medium">Введите пароль:</label>
                    <input type="password" name="password" id="password" required
                        class="w-full p-3 border border-gray-300 rounded-sm focus:border-black outline-none transition">
                    @error('password')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ======== ЗАПОМНИТЬ МЕНЯ / ЗАБЫЛИ ПАРОЛЬ ======== --}}
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between text-sm text-gray-700 space-y-2 md:space-y-0">

                    <!-- Ссылка будет первой на мобилке -->
                    <a href="#" class="hover:underline order-1 md:order-2">
                        Забыли пароль?
                    </a>

                    <!-- Чекбокс будет вторым на мобилке -->
                    <label class="flex items-center order-2 md:order-1">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-400">
                        Запомнить меня
                    </label>

                </div>

                {{-- ======== КНОПКА ======== --}}
                <button type="submit" class="py-3 px-6 bg-black text-white rounded-sm hover:opacity-85 transition text-lg">
                    Войти
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-700">
                Нет аккаунта?
                <a href="{{ route('register') }}" class="text-gray-900 font-medium hover:underline">Регистрация</a>
            </p>
        </div>
    </div>
@endsection
