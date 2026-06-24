@extends('layouts.admin')

@section('title', 'Редактировать товар')

@section('content')
    <div class="container py-6">

        {{-- SUCCESS --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- ===================== РЕДАКТИРОВАНИЕ ТОВАРА ===================== --}}
        <div class="card shadow mb-5 p-4">
            <h4 class="card-title mb-4">Редактировать товар: {{ $product->title }}</h4>

            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Название --}}
                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $product->title) }}"
                        required>
                </div>

                {{-- Описание --}}
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>

                {{-- Инструкция по уходу --}}
                <div class="mb-3">
                    <label class="form-label">Инструкция по уходу</label>
                    <textarea name="care_instructions" rows="2" class="form-control">{{ old('care_instructions', $product->care_instructions) }}</textarea>
                </div>

                <input type="hidden" name="deleted_images" id="deletedImages">

                {{-- Категории --}}
                <div class="mb-3">
                    <label class="form-label mb-2">Категория</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($categories as $category)
                            <div class="form-check border rounded px-3 py-2" style="cursor:pointer;">
                                <input class="form-check-input" type="radio" name="category_id"
                                    id="cat-{{ $category->id }}" value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'checked' : '' }}>
                                <label class="form-check-label ms-2"
                                    for="cat-{{ $category->id }}">{{ $category->title }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Размеры --}}
                <div class="mb-3">
                    <label class="form-label">Размеры</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($sizes as $size)
                            <div class="form-check border rounded px-3 py-1" style="cursor:pointer;">
                                <input class="form-check-input" type="checkbox" name="sizes[]"
                                    id="size-{{ $size->id }}" value="{{ $size->id }}"
                                    {{ in_array($size->id, old('sizes', $product->sizes->pluck('id')->toArray())) ? 'checked' : '' }}
                                    <label class="form-check-label ms-2"
                                    for="size-{{ $size->id }}">{{ $size->title }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ===================== ЦВЕТА + СКЛАД/ЦЕНА ===================== --}}
                <div class="mb-10">

                    <label class="block text-gray-500 mb-4 font-medium">
                        Цвета товара
                    </label>

                    @php
                        $selectedColors = old('colors', $product->productColors?->pluck('color_id')->toArray() ?? []);
                    @endphp

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">

                        @foreach ($colors as $color)
                            @php
                                $checked = in_array($color->id, $selectedColors);
                                $productColor = $product->productColors->firstWhere('color_id', $color->id);
                            @endphp

                            <label
                                class="relative border rounded-lg p-3 cursor-pointer bg-white transition flex flex-col justify-between"
                                style="height: 160px;"> {{-- фиксированная высота карточки --}}

                                <input type="checkbox" name="colors[]" value="{{ $color->id }}"
                                    class="hidden peer color-selector" data-id="{{ $color->id }}"
                                    {{ $checked ? 'checked' : '' }}>

                                {{-- верх --}}
                                <div class="flex items-center justify-between mb-2">

                                    <div class="w-6 h-6 rounded border" style="background: {{ $color->code }}"></div>

                                    <div
                                        class="text-xs px-2 py-1 rounded
                        {{ $checked ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $checked ? 'выбрано' : 'не выбрано' }}
                                    </div>

                                </div>

                                <div class="text-sm text-gray-700 mb-2">
                                    {{ $color->title }}
                                </div>

                                {{-- мини-превью фото + загрузка --}}
                                <div id="upload-{{ $color->id }}" class="rounded-sm {{ $checked ? '' : 'hidden' }}">

                                    {{-- превью (старые фото) --}}
                                    <div class="flex items-center gap-2 overflow-x-auto pb-1">

                                        @if ($productColor && $productColor->images->count())
                                            @foreach ($productColor->images as $img)
                                                <div class="relative w-12 h-12 group image-wrapper"
                                                    data-id="{{ $img->id }}">

                                                    <img src="{{ Storage::url($img->path) }}"
                                                        class="w-12 h-12 object-cover rounded-md border shadow-sm transition group-hover:opacity-50">

                                                    {{-- ОДИН СЛОЙ НА ВСЮ КАРТИНКУ --}}
                                                    <button type="button"
                                                        class="delete-image-btn absolute inset-0 w-full h-full
                   flex items-center justify-center
                   text-white text-2xl font-light
                   bg-black/0 group-hover:bg-black/40
                   opacity-0 group-hover:opacity-100
                   transition">
                                                        ×
                                                    </button>

                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="preview-{{ $color->id }} flex items-center gap-2"></div>

                                        <label
                                            class="w-12 h-12 flex items-center justify-center border border-dashed rounded-md cursor-pointer">
                                            <input type="file" name="color_images[{{ $color->id }}][]" multiple
                                                class="hidden color-file-input" data-id="{{ $color->id }}">
                                            <span class="text-xl">+</span>
                                        </label>

                                    </div>

                                </div>

                            </label>
                        @endforeach

                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {

                        // toggle блоков цветов
                        document.querySelectorAll('.color-selector').forEach(cb => {
                            const block = document.getElementById('upload-' + cb.dataset.id);

                            const toggle = () => {
                                block.classList.toggle('hidden', !cb.checked);
                            };

                            toggle();
                            cb.addEventListener('change', toggle);
                        });

                        // PREVIEW файлов (ВАЖНО)
                        document.querySelectorAll('.color-file-input').forEach(input => {

                            input.addEventListener('change', (e) => {

                                const id = input.dataset.id;
                                const preview = document.querySelector('.preview-' + id);

                                preview.innerHTML = '';

                                const files = Array.from(e.target.files);

                                files.forEach(file => {

                                    const reader = new FileReader();

                                    reader.onload = (ev) => {
                                        const img = document.createElement('img');
                                        img.src = ev.target.result;
                                        img.className = 'w-12 h-12 object-cover rounded border';
                                        preview.appendChild(img);
                                    };

                                    reader.readAsDataURL(file);
                                });
                            });
                        });

                    });
                </script>

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

                {{-- Количество и цена --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Количество на складе</label>
                        <input type="number" name="stock" min="0" class="form-control"
                            value="{{ old('stock', $product->sizes->first()?->pivot?->stock ?? 0) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Цена (₽)</label>
                        <input type="number" step="0.01" name="price" min="0" class="form-control"
                            value="{{ old('price', $product->sizes->first()?->pivot?->price ?? 0) }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-dark mb-4">Сохранить изменения</button>
            </form>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            let deletedImages = [];

            document.querySelectorAll('.delete-image-btn').forEach(btn => {

                btn.addEventListener('click', function() {

                    const wrapper = this.closest('.image-wrapper');

                    const id = wrapper.dataset.id;

                    if (!deletedImages.includes(id)) {
                        deletedImages.push(id);
                    }

                    document.getElementById('deletedImages').value =
                        JSON.stringify(deletedImages);

                    wrapper.style.display = 'none';
                });

            });

        });
    </script>
@endsection
