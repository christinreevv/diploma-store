<tr class="border-b">
    <td class="p-3">{{ $item->product->name }}</td>
    <td class="p-3 text-center">{{ $item->quantity }}</td>
    <td class="p-3 text-center">{{ $item->product->price }} ₽</td>
    <td class="p-3 text-center">{{ $item->quantity * $item->product->price }} ₽</td>
    <td class="p-3 text-right">
        <form method="POST" action="{{ route('cart.remove', $item->id) }}">
            @csrf
            @method('DELETE')
            <button class="text-red-500 hover:text-red-700">✕</button>
        </form>
    </td>
</tr>
