<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // 🛒 Показываем корзину
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
        ])->where('user_id', auth()->id())->first();

        // Общая сумма корзины
        $total = $cart->items->sum(fn ($item) => ($item->price ?? 0) * $item->quantity
        );

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, $slug)
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
            ], 401);
        }

        $request->validate([
            'color' => 'required',
            'size_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('slug', $slug)
            ->with(['productColors.color', 'sizes'])
            ->firstOrFail();

$productColor = $product->productColors()
    ->whereHas('color', function ($q) use ($request) {
        $q->where('code', $request->color);
    })
    ->first();

        $productSize = \App\Models\ProductSize::where('product_id', $product->id)
            ->where('size_id', $request->size_id)
            ->first();
        if (! $productColor || ! $productSize) {
            return response()->json([
                'success' => false,
            ], 422);
        }

        $cart = Auth::user()->cart()->first();

        if (! $cart) {
            $cart = Auth::user()->cart()->create();
        }

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_color_id', $productColor->id)
            ->where('product_size_id', $productSize->id)
            ->first();

        if ($item) {

            $item->update([
                'quantity' => $request->quantity,
            ]);

        } else {

            $cart->items()->create([
                'product_id' => $product->id,
                'product_color_id' => $productColor->id,
                'product_size_id' => $productSize->id,
                'price' => $productSize->price ?? 0,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    // 🔄 Обновление количества товара
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

    // ❌ Удаление товара из корзины
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
