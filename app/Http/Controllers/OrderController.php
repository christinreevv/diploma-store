<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create()
    {
        $cart = Auth::user()->cart()
            ->with([
                'items.product',
                'items.productColor.images',
                'items.productSize',
            ])
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Корзина пуста');
        }

        return view('orders.create', compact('cart'));
    }

    public function store(Request $request)
    {
        // ✅ ВАЛИДАЦИЯ НОВОЙ СТРУКТУРЫ АДРЕСА
        $request->validate([
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:150',
            'house' => 'required|string|max:20',
            'apartment' => 'nullable|string|max:20',
            'postal_code' => 'nullable|string|max:20',
            'payment_method' => 'required|string',
        ]);

        $cart = Auth::user()->cart()->with('items')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        // 💰 СЧЁТ ИТОГО
        $total = $cart->items->sum(function ($item) {
            return ($item->price ?? 0) * $item->quantity;
        });

        // 📦 СОЗДАНИЕ ЗАКАЗА
        $order = Order::create([
            'user_id' => Auth::id(),
          'status' => 'Новый',
            'total_price' => $total,

            'city' => $request->city,
            'street' => $request->street,
            'house' => $request->house,
            'apartment' => $request->apartment,
            'postal_code' => $request->postal_code,

            'payment_method' => $request->payment_method,
        ]);

        // 📦 ITEMS
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_color_id' => $item->product_color_id,
                'product_size_id' => $item->product_size_id,

                'price' => $item->price ?? 0,
                'quantity' => $item->quantity,
            ]);
        }

        // 🧹 очистка корзины
        $cart->items()->delete();

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Заказ оформлен');
    }

    public function show(Order $order)
    {
        $order->load([
            'items.product',
            'items.productColor.images',
            'items.productSize'
        ]);

        return view('orders.show', compact('order'));
    }
}
