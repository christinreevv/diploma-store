@extends('layouts.admin')

@section('title', 'Новинки')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Новинки', 'url' => route('new-arrivals')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')

    <div class="container mx-auto py-8">

        {{-- Hero --}}
        {{-- Hero --}}
      {{-- Hero --}}
<div class="mb-12">

    <div>
        <span class="uppercase tracking-[0.25em] text-sm text-gray-500">
            New Arrivals
        </span>

        <h1 class="text-4xl lg:text-5xl font-light mt-3">
            Новинки
        </h1>
    </div>

    <div class="flex justify-end mt-6">
        <x-view-switch />
    </div>

</div>

        {{-- Товары --}}
        @include('catalog._products', ['products' => $products])

    </div>

@endsection
