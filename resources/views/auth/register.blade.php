@extends('layouts.admin')

@section('title', 'Регистрация')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-90 to-gray-200 p-4">
        <div class="w-full max-w-md backdrop-blur p-8">
            <h1 class="text-3xl font-medium mb-6 text-center text-gray-800">Регистрация</h1>

            <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-6 w-full max-w-md mx-auto">
                @csrf

                {{-- ======== ИМЯ ======== --}}
                <div>
                    <label class="block mb-1 font-medium">Имя</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full p-3 border border-gray-300 rounded-sm focus:border-black outline-none transition">
                    @error('name')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ======== EMAIL ======== --}}
                <div class="relative">
                    <label class="block mb-1 font-medium">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full p-3 border border-gray-300 rounded-sm focus:border-black outline-none transition">
                    @error('email')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ======== ПАРОЛЬ ======== --}}
                <div>
                    <label class="block mb-1 font-medium">Пароль</label>
                    <input type="password" name="password" required
                        class="w-full p-3 border border-gray-300 rounded-sm focus:border-black outline-none transition">
                    @error('password')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ======== ПОДТВЕРЖДЕНИЕ ПАРОЛЯ ======== --}}
                <div>
                    <label class="block mb-1 font-medium">Повторите пароль</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full p-3 border border-gray-300 rounded-sm focus:border-black outline-none transition">
                </div>

                {{-- ======== СОГЛАСИЕ ======== --}}
                <div>
                    <label class="flex items-start gap-3 text-sm text-gray-700 leading-5 cursor-pointer">
                        <input type="checkbox" name="privacy_agreement" required
                            class="mt-1 h-4 w-4 border border-gray-300 rounded-sm text-black focus:ring-0 focus:outline-none">

                        <span>
                            Я даю согласие на обработку моих персональных данных и принимаю условия
                            пользовательского соглашения.
                        </span>
                    </label>
                </div>

                {{-- ======== КНОПКА ======== --}}
                <button type="submit" class="py-3 px-6 bg-black text-white rounded-sm hover:opacity-85 transition text-lg">
                    Зарегистрироваться
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-700">
                Уже есть аккаунт?
                <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:underline">Войти</a>
            </p>
        </div>
    </div>
@endsection
