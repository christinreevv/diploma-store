@extends('layouts.admin')

@section('title', 'Админка товаров')

@section('content')
    <div class="min-h-screen bg-gray-50 py-10">

        <div class="container mb-5">

            <h2
                style="font-family: 'Comfortaa', sans-serif; font-weight: 400; font-size: 1.875rem; line-height: 2.25rem; margin-bottom: 2.5rem; color: #4B5563;">
                Добавление товара
            </h2>

            {{-- SUCCESS --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="mb-10 px-4 py-3 rounded-lg bg-indigo-50 text-indigo-800 flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="font-bold">&times;</button>
                </div>
            @endif

            {{-- ===================== ДОБАВЛЕНИЕ ТОВАРА ===================== --}}
            <div class="mb-16">

                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label">Название</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required
                            style="
            transition: border-color .2s ease, box-shadow .2s ease;
        "
                            onfocus="this.style.borderColor='#9CA3AF'; this.style.boxShadow='0 0 0 2px rgba(156,163,175,.25)'"
                            onblur="this.style.borderColor=''; this.style.boxShadow=''">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Описание</label>
                        <textarea name="description" rows="3" class="form-control"
                            style="transition: border-color .2s ease, box-shadow .2s ease;"
                            onfocus="this.style.borderColor='#9CA3AF'; this.style.boxShadow='0 0 0 2px rgba(156,163,175,.25)'"
                            onblur="this.style.borderColor=''; this.style.boxShadow=''">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="form-label">Инструкция по уходу</label>
                        <textarea name="care_instructions" rows="2" class="form-control"
                            style="transition: border-color .2s ease, box-shadow .2s ease;"
                            onfocus="this.style.borderColor='#9CA3AF'; this.style.boxShadow='0 0 0 2px rgba(156,163,175,.25)'"
                            onblur="this.style.borderColor=''; this.style.boxShadow=''">{{ old('care_instructions') }}</textarea>
                    </div>


                    {{-- ===================== КАТЕГОРИИ + РАЗМЕРЫ ===================== --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-8">

                        {{-- Левая колонка: Категории --}}
                        <div>
                            <label class="block text-gray-500 mb-3 font-medium">Категории</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($categories as $category)
                                    <label
                                        class="cursor-pointer px-4 py-2 rounded-lg bg-white text-gray-600 transition-all duration-200 hover:bg-gray-50 peer-checked:bg-gray-100">
                                        <input type="radio" name="category_id" value="{{ $category->id }}"
                                            class="sr-only peer" {{ old('category_id') == $category->id ? 'checked' : '' }}>
                                        <span class="transition-all peer-checked:text-gray-900 peer-checked:font-medium">
                                            {{ $category->title }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Правая колонка: Размеры --}}
                        <div>
                            <label class="block text-gray-500 mb-3 font-medium">Размеры</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sizes as $size)
                                    <label
                                        class="cursor-pointer px-4 py-2 rounded-lg bg-white text-gray-600 transition-all duration-200 hover:bg-gray-50 peer-checked:bg-gray-100">
                                        <input type="checkbox" name="sizes[]" value="{{ $size->id }}"
                                            class="sr-only peer"
                                            {{ is_array(old('sizes')) && in_array($size->id, old('sizes')) ? 'checked' : '' }}>
                                        <span class="transition-all peer-checked:text-gray-900 peer-checked:font-medium">
                                            {{ $size->title }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    {{-- ===================== ЦВЕТА (КАРТОЧКИ) ===================== --}}
                    <div class="mb-10">

                        <label class="block text-gray-500 mb-4 font-medium">
                            Цвета товара
                        </label>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">

                            @foreach ($colors as $color)
                                @php
                                    $checked = is_array(old('colors')) && in_array($color->id, old('colors'));
                                @endphp

                                <label
                                    class="relative border rounded-lg p-3 cursor-pointer bg-white hover:shadow transition">

                                    {{-- checkbox скрытый --}}
                                    <input type="checkbox" name="colors[]" value="{{ $color->id }}"
                                        class="hidden peer color-selector" data-id="{{ $color->id }}"
                                        {{ $checked ? 'checked' : '' }}>

                                    {{-- карточка цвета --}}
                                    <div class="flex items-center justify-between mb-2">

                                        <div class="w-6 h-6 rounded border" style="background: {{ $color->code }}">
                                        </div>

                                        <div
                                            class="text-xs px-2 py-1 rounded
                        {{ $checked ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $checked ? 'выбран' : 'не выбран' }}
                                        </div>

                                    </div>

                                    <div class="text-sm text-gray-700">
                                        {{ $color->title }}
                                    </div>

                                    {{-- загрузка фото --}}
                                    <div id="upload-{{ $color->id }}" class="mt-3 {{ $checked ? '' : 'hidden' }}">

                                        <input type="file" name="color_images[{{ $color->id }}][]" multiple
                                            class="w-full text-xs border rounded p-1">
                                    </div>

                                </label>
                            @endforeach

                        </div>
                    </div>

                    <div class="mb-8">

                        <label class="block text-gray-500 mb-3 font-medium">
                            Особенности товара
                        </label>

                        <label id="limitedBox" class="cursor-pointer block border rounded-lg p-4 bg-white transition">

                            <input type="checkbox" name="is_limited" value="1" id="is_limited" class="hidden"
                                {{ old('is_limited') ? 'checked' : '' }}>

                            <div class="flex items-start justify-between">

                                <div>
                                    <div class="font-medium">
                                        Лимитированная коллекция
                                    </div>

                                    <div class="text-sm text-gray-500 mt-1">
                                        Товар будет отмечаться как эксклюзивный
                                    </div>
                                </div>

                                <div id="checkIcon" class="text-xl opacity-0 transition">
                                    ✓
                                </div>

                            </div>

                        </label>

                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const checkbox = document.getElementById('is_limited');
                            const box = document.getElementById('limitedBox');
                            const icon = document.getElementById('checkIcon');

                            function updateUI() {
                                if (checkbox.checked) {
                                    box.classList.add('bg-gray-100', 'border-black');
                                    icon.classList.add('opacity-100');
                                    icon.classList.remove('opacity-0');
                                } else {
                                    box.classList.remove('bg-gray-100', 'border-black');
                                    icon.classList.add('opacity-0');
                                    icon.classList.remove('opacity-100');
                                }
                            }

                            checkbox.addEventListener('change', updateUI);

                            updateUI(); // при загрузке
                        });
                    </script>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            document.querySelectorAll('.color-selector').forEach(cb => {
                                const block = document.getElementById(`upload-${cb.dataset.key}`);
                                block.classList.toggle('d-none', !cb.checked);
                                cb.addEventListener('change', () => {
                                    block.classList.toggle('d-none', !cb.checked);
                                });
                            });
                        });
                    </script>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {

                            document.querySelectorAll('.color-selector').forEach(cb => {

                                const id = cb.dataset.id;
                                const block = document.getElementById('upload-' + id);

                                const toggle = () => {
                                    if (cb.checked) {
                                        block.classList.remove('hidden');
                                    } else {
                                        block.classList.add('hidden');
                                    }
                                };

                                toggle();
                                cb.addEventListener('change', toggle);

                            });

                        });
                    </script>
            </div>


            <div class="row mb-6">
                <div class="col-md-6">
                    <label class="form-label">Количество на складе</label>
                    <input type="text" name="stock" inputmode="numeric" pattern="[0-9]*"
                        class="form-control clear-zero only-integer rounded-sm" value="{{ old('stock', $stock ?? 0) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Цена (₽)</label>
                    <input type="text" name="price" inputmode="decimal"
                        class="form-control clear-zero only-decimal rounded-sm" value="{{ old('price', $price ?? 0) }}">
                </div>
            </div>


            <button type="submit" class="btn btn-dark px-3 py-2 rounded-sm">
                Добавить товар
            </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.clear-zero').forEach(input => {

                input.addEventListener('focus', () => {
                    if (input.value === '0') {
                        input.value = '';
                    }
                });

                input.addEventListener('blur', () => {
                    if (input.value === '') {
                        input.value = '0';
                    }
                });

            });
        });

        document.addEventListener('DOMContentLoaded', () => {

            // Только целые числа (stock)
            document.querySelectorAll('.only-integer').forEach(input => {
                input.addEventListener('input', () => {
                    input.value = input.value.replace(/\D/g, '');
                });
            });

            // Число с точкой (price)
            document.querySelectorAll('.only-decimal').forEach(input => {
                input.addEventListener('input', () => {
                    input.value = input.value
                        .replace(',', '.')
                        .replace(/[^0-9.]/g, '')
                        .replace(/(\..*)\./g, '$1');
                });
            });

        });
    </script>

@endsection
