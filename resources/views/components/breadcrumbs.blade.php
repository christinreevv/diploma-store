@props(['items' => []])

@if (!empty($items))
    <nav class="bg-gray-10">
        <div class="container mx-auto px-6 py-3">
            <ol class="flex items-center space-x-2 text-[14px] text-gray-600"
                style="font-family: 'Montserrat', sans-serif;">
                @foreach ($items as $index => $item)
                    <li class="flex items-center">
                        @if (!$loop->first)
                            <span class="mx-2">/</span>
                        @endif

                        @if (isset($item['url']) && !$loop->last)
                            <a href="{{ $item['url'] }}" class="transition-colors hover:text-red-600">
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </nav>
@endif
