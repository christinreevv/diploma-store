<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items'])
            ->latest()
            ->paginate(12);

        return view('admin.orders.index', compact('orders'));
    }

    public function toggleStatus(Order $order)
    {
        $order->status = $order->status === 'completed'
            ? 'pending'
            : 'completed';

        $order->save();

        return response()->json([
            'status' => $order->status,
        ]);
    }
}
