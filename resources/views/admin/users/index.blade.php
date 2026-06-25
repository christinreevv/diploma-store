@extends('layouts.admin')

@section('title', 'Пользователи')



@section('breadcrumbs')
    @php
        $breadcrumbs = [['label' => 'Главная', 'url' => url('/')], ['label' => 'Пользователи', 'url' => '#']];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
    <div class="container mx-auto">

        {{-- Заголовок и поиск --}}
        <div class="flex flex-col md:flex-row md:items-center mb-8">

            {{-- HEADER --}}
            <div class="flex items-center justify-between py-10">
                <h1 class="text-3xl font-light text-gray-700">
                    Все пользователи
                </h1>
            </div>

            {{-- Поиск справа --}}
            <div class="md:ml-auto md:mt-0">

                <form method="GET"
                    class="flex items-center bg-gray-50 border border-gray-300 rounded-sm px-4 py-2 shadow-sm focus-within:ring-2 focus-within:ring-gray-400 transition w-full md:w-auto">

                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Поиск по имени или email"
                        class="bg-transparent focus:outline-none text-gray-700 placeholder-gray-400 text-sm w-full md:w-56">

                    <button type="submit" class="ml-2 text-gray-700 hover:text-black transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.85-5.4a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                </form>

            </div>

        </div>

        {{-- USERS GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

            @forelse ($users as $u)
                <div class="border border-gray-200 bg-white hover:border-gray-300 transition group">

                    {{-- TOP --}}
                    <div class="p-5 flex items-start justify-between">

                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-400">
                               Пользователь #{{ $users->firstItem() + $loop->index }}
                            </p>

                            <h3 class="mt-2 text-lg font-medium text-gray-900 group-hover:text-black transition">
                                {{ $u->name }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $u->email }}
                            </p>
                        </div>

                        {{-- AVATAR --}}
                        @if ($u->avatar)
                            <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $u->name }}"
                                class="w-10 h-10 rounded-full object-cover border border-gray-200">
                        @else
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-medium text-gray-600">
                                {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                            </div>
                        @endif

                    </div>

                    {{-- MIDDLE --}}
                    <div class="px-5 pb-5 space-y-3">

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Регистрация</span>
                            <span class="text-gray-900">
                                {{ $u->created_at->format('d.m.Y') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Время</span>
                            <span class="text-gray-900">
                                {{ $u->created_at->format('H:i') }}
                            </span>
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">

                        <span class="text-xs text-gray-400">
                          № {{ $users->firstItem() + $loop->index }}
                        </span>

                        <a href="{{ route('admin.users.show', $u) }}"
                            class="text-sm text-gray-500 hover:text-black transition">
                            Открыть
                        </a>
                    </div>

                </div>

            @empty

                <div class="col-span-full text-center text-gray-400 py-20">
                    Пользователи не найдены
                </div>
            @endforelse

        </div>

        @if ($users->hasPages())
            <div class="mt-10 flex justify-center">
                <div class="px-4 py-2 border border-gray-200 bg-white">
                    {{ $users->links() }}
                </div>
            </div>
        @endif

    </div>
@endsection
