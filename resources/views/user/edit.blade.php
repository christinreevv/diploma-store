@extends('layouts.admin')

@section('title', 'Редактировать профиль')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Профиль', 'url' => route('profile.show')],
            ['label' => 'Редактирование', 'url' => route('profile.edit')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
<div class="container mx-auto mt-12 p-6 md:p-12 space-y-10">

    {{-- Профиль пользователя --}}
    <div class="flex flex-col md:flex-row md:space-x-10 space-y-6 md:space-y-0 items-start">
        {{-- Аватар --}}
        <div class="flex-shrink-0">
            <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                @csrf
                <label class="cursor-pointer relative block w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center text-3xl md:text-4xl font-semibold text-gray-500 hover:ring-2 hover:ring-gray-400 transition">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                    <input type="file" name="avatar" class="hidden" accept="image/*" onchange="this.form.submit()">
                  
                </label>
                @error('avatar')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </form>
        </div>

        {{-- Информация --}}
        <div class="flex-1 flex flex-col space-y-6 relative w-full">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="flex flex-col">
                    <p class="text-sm text-gray-500">Имя</p>
                    <p class="text-lg font-medium text-gray-900">{{ $user->name }}</p>
                </div>
                <div class="flex flex-col">
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="text-lg font-medium text-gray-900">{{ $user->email }}</p>
                </div>
            </div>

            {{-- Ссылка на админ-панель --}}
            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                   class="self-start text-black font-medium hover:underline mt-4">
                   Перейти в админ-панель
                </a>
            @endif
        </div>
    </div>

    {{-- Форма редактирования данных --}}
    <div class=" space-y-6">
        <h2 class="text-2xl font-medium text-gray-900">Редактировать данные</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col">
                    <label for="name" class="text-gray-500 font-regular mb-1">Имя</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full p-3 border border-gray-300 rounded-sm focus:ring-2 focus:ring-gray-400 focus:outline-none text-gray-900">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col">
                    <label for="email" class="text-gray-500 font-regular mb-1">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full p-3 border border-gray-300 rounded-sm focus:ring-2 focus:ring-gray-400 focus:outline-none text-gray-900">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                    class="inline-block border border-gray-900 text-gray-900 px-8 py-3 rounded-sm text-md font-medium
                  hover:bg-gray-900 hover:text-white transition duration-300 ease-in-out shadow-sm">
                Сохранить
            </button>
        </form>
    </div>

    {{-- Смена пароля --}}
    <div class="space-y-6">
        <h2 class="text-2xl font-medium text-gray-900">Сменить пароль</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex flex-col">
                    <label for="current_password" class="text-gray-500 font-regular mb-1">Текущий пароль</label>
                    <input type="password" id="current_password" name="current_password"
                           class="w-full p-3 border border-gray-300 rounded-sm focus:ring-2 focus:ring-gray-400 focus:outline-none text-gray-900">
                    @error('current_password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col">
                    <label for="new_password" class="text-gray-500 font-regular mb-1">Новый пароль</label>
                    <input type="password" id="new_password" name="new_password"
                           class="w-full p-3 border border-gray-300 rounded-sm focus:ring-2 focus:ring-gray-400 focus:outline-none text-gray-900">
                    @error('new_password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col">
                    <label for="new_password_confirmation" class="text-gray-500 font-regular mb-1">Повторите пароль</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                           class="w-full p-3 border border-gray-300 rounded-sm focus:ring-2 focus:ring-gray-400 focus:outline-none text-gray-900">
                    @error('new_password_confirmation')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                    class="inline-block border border-gray-900 text-gray-900 px-8 py-3 rounded-sm text-md font-medium
                  hover:bg-gray-900 hover:text-white transition duration-300 ease-in-out shadow-sm">
                Сменить пароль
            </button>
        </form>
    </div>

</div>
@endsection
