@extends('layouts.admin')

@section('content')
    <div class="max-w-6xl mx-auto">

        <h1 class="text-3xl font-semibold mb-8">
            Матчинг категорий
        </h1>

        @foreach ($categories as $category)
            <form method="POST"
                action="{{ route('admin.category-matches.update', $category) }}"
                class="mb-10 border rounded-lg p-6 bg-white">

                @csrf

                <h2 class="font-semibold text-xl mb-4">
                    {{ $category->title }}
                </h2>

                <div class="grid grid-cols-3 gap-3">

                    @foreach ($categories as $related)
                        @continue($related->id === $category->id)

                        <label class="flex items-center gap-2">

                            <input type="checkbox" name="matched_categories[]" value="{{ $related->id }}"
                                {{ $category->matches->contains($related->id) ? 'checked' : '' }}>

                            <span>
                                {{ $related->title }}
                            </span>

                        </label>
                    @endforeach

                </div>

                <button class="mt-5 px-4 py-2 bg-black text-white rounded">
                    Сохранить
                </button>

            </form>
        @endforeach

        <hr class="my-12">

        <h1 class="text-3xl font-semibold mb-8">
            Матчинг цветов
        </h1>

        @foreach ($colors as $color)
            <form method="POST"
                action="{{ route('admin.color-matches.update', $color) }}"
                class="mb-10 border rounded-lg p-6 bg-white">

                @csrf

                <div class="flex items-center gap-3 mb-4">

                    <div class="w-6 h-6 rounded-full border" style="background: {{ $color->code }}"></div>

                    <h2 class="font-semibold text-xl">
                        {{ $color->title }}
                    </h2>

                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                    @foreach ($colors as $related)
                        @continue($related->id === $color->id)

                        <label class="flex items-center gap-2 border rounded p-2 hover:bg-gray-50">

                            <input type="checkbox" name="matched_colors[]" value="{{ $related->id }}"
                                {{ $color->matches->contains($related->id) ? 'checked' : '' }}>

                            <div class="w-4 h-4 rounded-full border" style="background: {{ $related->code }}"></div>

                            <span>
                                {{ $related->title }}
                            </span>

                        </label>
                    @endforeach

                </div>

                <button class="mt-5 px-4 py-2 bg-black text-white rounded">
                    Сохранить
                </button>

            </form>
        @endforeach

    </div>
@endsection
