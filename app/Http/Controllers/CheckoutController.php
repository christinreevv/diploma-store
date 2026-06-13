<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::with('items.product', 'items.productSize', 'items.productColor')
            ->where('user_id', auth()->id())
            ->first();

        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $cart = Cart::with('items')->where('user_id', auth()->id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Корзина пустая');
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'total_price' => 0,
            'delivery_address' => $request->delivery_address,
            'payment_method' => $request->payment_method,
        ]);

        $total = 0;

        foreach ($cart->items as $item) {

            $price = $item->price; // уже сохранённая цена

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_size_id' => $item->product_size_id,
                'product_color_id' => $item->product_color_id,
                'quantity' => $item->quantity,
                'price' => $price,
            ]);

            $total += $price * $item->quantity;
        }

        $order->update([
            'total_price' => $total
        ]);

        // очистка корзины
        $cart->items()->delete();

        return redirect()->route('home')->with('success', 'Заказ оформлен!');
    }
}
