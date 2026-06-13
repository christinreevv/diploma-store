@extends('layouts.admin')

@section('title', 'Лимитированная коллекция')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['label' => 'Главная', 'url' => url('/')],
            ['label' => 'Лимитированная коллекция', 'url' => route('collections.limited')],
        ];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')

    <div class="container mx-auto py-10">

        {{-- HERO --}}
        <div class="mb-10">

            <span class="uppercase tracking-[0.35em] text-xs text-gray-400">
                Exclusive Drop
            </span>

            <h1 class="text-5xl font-light mt-4">
                Лимитированная коллекция
            </h1>

        </div>

        {{-- VIEW SWITCH --}}
        <x-view-switch />

        {{-- Товары --}}
        @include('catalog._products', ['products' => $products])

    </div>

@endsection
