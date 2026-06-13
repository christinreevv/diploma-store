@foreach ($products as $product)

    <a href="{{ route('products.show', $product->slug) }}"
        data-suggestion
        class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">

        <div class="text-sm font-medium">
            {{ $product->title }}
        </div>

    </a>

@endforeach
