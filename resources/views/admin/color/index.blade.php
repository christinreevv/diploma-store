@extends('layouts.admin')

@section('title', 'Цвета')

@section('content')

<div class="mb-12 py-8">

    {{-- стрелка назад --}}
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-black transition mb-6">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 19l-7-7 7-7"/>
        </svg>

        Назад
    </a>

    <h2 class="text-2xl font-light text-gray-600 mb-8">
        Цвета товара
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-4">

        @foreach ($colors as $color)
            <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow transition">

                <div class="flex items-center justify-between mb-3">

                    <div
                        class="w-6 h-6 rounded border border-gray-200"
                        style="background: {{ $color->code }}">
                    </div>

                    <form action="{{ route('admin.colors.update', $color) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="text-xs px-2 py-1 rounded
                            {{ $color->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $color->is_active ? 'Активен' : 'Неактивен' }}
                        </button>
                    </form>

                </div>

                <div class="text-sm text-gray-700 truncate">
                    {{ $color->title }}
                </div>

            </div>
        @endforeach

    </div>

</div>

@endsection
