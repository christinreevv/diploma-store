@extends('layouts.admin')

@section('title', 'Категории')

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
        Категории
    </h2>

    {{-- форма добавления --}}
    <form action="{{ route('admin.categories.store') }}" method="POST"
          class="flex gap-2 mb-8">
        @csrf

        <input type="text"
               name="title"
               placeholder="Название категории"
               class="border border-gray-300 px-3 py-2 rounded w-full focus:outline-none focus:border-black">

        <button class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800 transition">
            Добавить
        </button>
    </form>

    {{-- карточки --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">

        @foreach ($categories as $category)
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow transition">

                {{-- название --}}
                <form action="{{ route('admin.categories.update', $category) }}"
                      method="POST"
                      class="flex items-center justify-between gap-2">

                    @csrf
                    @method('PUT')

                    <input type="text"
                           name="title"
                           value="{{ $category->title }}"
                           class="text-sm w-full border-0 focus:ring-0 p-0 text-gray-700 bg-transparent">

                    <button class="text-xs text-gray-400 hover:text-black">
                        ✎
                    </button>
                </form>

                {{-- удалить --}}
                <form action="{{ route('admin.categories.destroy', $category) }}"
                      method="POST"
                      class="mt-3"
                      onsubmit="return confirm('Удалить категорию?')">

                    @csrf
                    @method('DELETE')

                    <button class="text-xs text-red-500 hover:text-red-700">
                        Удалить
                    </button>

                </form>

            </div>
        @endforeach

    </div>

</div>

@endsection
