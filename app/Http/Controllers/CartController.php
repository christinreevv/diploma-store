<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // -------------------------------------------- отображение корзины --------------------------------------------
    public function index()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $cart = Cart::with([
            'items.product',
            'items.productColor.color',
            'items.productColor.images',
            'items.productSize.size',
        ])->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        $total = $cart->items->sum(fn ($item) => ($item->price ?? 0) * $item->quantity
        );

        return view('cart.index', compact('cart', 'total'));
    }

    // -------------------------------------------- добавление в корзину --------------------------------------------

public function add(Request $request, $slug)
{
    if (!Auth::check()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $request->validate([
        'color' => 'required',
        'size_id' => 'required|exists:sizes,id',
    ]);

    $product = Product::where('slug', $slug)
        ->with(['productColors.color', 'sizes'])
        ->firstOrFail();

    $productColor = $product->productColors()
        ->where('color_id', $request->color)
        ->first();

    $productSize = ProductSize::where('product_id', $product->id)
        ->where('size_id', $request->size_id)
        ->firstOrFail();

    if (!$productColor || !$productSize) {
        return response()->json(['success' => false, 'message' => 'Invalid data'], 422);
    }

    $cart = Cart::firstOrCreate([
        'user_id' => Auth::id(),
    ]);

    $item = $cart->items()
        ->where('product_id', $product->id)
        ->where('product_color_id', $productColor->id)
        ->where('product_size_id', $productSize->id)
        ->first();

    if ($item) {
        $item->increment('quantity', 1);
    } else {
        $item = $cart->items()->create([
            'product_id' => $product->id,
            'product_color_id' => $productColor->id,
            'product_size_id' => $productSize->id,
            'price' => $productSize->price,
            'quantity' => 1,
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Товар добавлен в корзину',
        'quantity' => $item->quantity,
    ]);
}

    // -------------------------------------------- обновление корзины --------------------------------------------

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::whereHas('cart', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->findOrFail($itemId);

        $item->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Количество обновлено');
    }

    // -------------------------------------------- удаление корзины --------------------------------------------

    public function remove($itemId)
    {
        $item = CartItem::whereHas('cart', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->findOrFail($itemId);

        $item->delete();

        return back()->with('success', 'Товар удалён');
    }

    // 🗑 Очистка корзины
    public function clear()
    {
        $cart = Auth::user()->cart;

        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('success', 'Корзина очищена');
    }
}
