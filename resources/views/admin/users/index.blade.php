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

            <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">

            </h1>

            {{-- Поиск справа --}}
            <div class="md:ml-auto  md:mt-0">

                <form method="GET"
                    class="flex items-center bg-gray-50 border border-gray-300 rounded-sm px-4 py-2 shadow-sm focus-within:ring-2 focus-within:ring-gray-400 transition w-full md:w-auto">

                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Поиск по имени, email или ID"
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

        {{-- Для больших экранов: таблица --}}
        <div class="hidden md:block bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <th class="p-4">ID</th>
                        <th class="p-4">Имя</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Дата регистрации</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($users as $u)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="p-4 font-medium text-gray-900">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                            <td class="p-4">{{ $u->name }}</td>
                            <td class="p-4 text-gray-700">{{ $u->email }}</td>
                            <td class="p-4 text-gray-500">{{ $u->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-gray-400 text-sm">
                                Пользователи не найдены
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Для мобильных экранов: карточки --}}
        <div class="md:hidden space-y-4">
            @forelse ($users as $u)
                <div class="bg-white shadow-sm border border-gray-100 rounded-sm p-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <p class="font-medium text-gray-900">ID: {{ $u->id }}</p>
                        <span class="text-gray-500 text-sm">{{ $u->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div>
                        <p class="text-gray-700 text-sm"><span class="font-medium">Имя:</span> {{ $u->name }}</p>
                        <p class="text-gray-700 text-sm"><span class="font-medium">Email:</span> {{ $u->email }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-center text-sm">Пользователи не найдены</p>
            @endforelse
        </div>

        {{-- Пагинация --}}
        {{-- <div class="mt-8 flex justify-center">
        {{ $users->links('vendor.pagination.tailwind') }}
    </div> --}}

    </div>
@endsection
