<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        Carbon::setLocale('ru');

        $orders = Order::with('items.product')->get();

        // =====================
        // 📦 ТОП ТОВАРОВ
        // =====================
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.title',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as orders_count')
            )
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // =====================
        // 📊 ОСНОВНЫЕ МЕТРИКИ
        // =====================
        $totalOrders = $orders->count();

        $totalRevenue = $orders->sum(fn ($order) => $order->items->sum(fn ($item) => $item->price * $item->quantity)
        );

        $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // =====================
        // ⏰ ПО ЧАСАМ
        // =====================
        $ordersByHour = $orders
            ->groupBy(fn ($o) => Carbon::parse($o->created_at)->format('H'))
            ->map->count();

        $peakHour = $ordersByHour->sortDesc()->keys()->first() ?? '—';

        // =====================
        // 📅 ПО ДНЯМ НЕДЕЛИ
        // =====================
        $ordersByDay = $orders
            ->groupBy(fn ($o) => Carbon::parse($o->created_at)->dayOfWeekIso)
            ->map->count();

        // =====================
        // 📈 ВЫРУЧКА ПО ДНЯМ
        // =====================
        $revenueByDay = $orders
            ->groupBy(fn ($o) => Carbon::parse($o->created_at)->format('Y-m-d'))
            ->map(fn ($dayOrders) => $dayOrders->sum(fn ($o) => $o->items->sum(fn ($i) => $i->price * $i->quantity)
            )
            );

        $frequentlyOrdered = \DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.title',
                \DB::raw('SUM(order_items.quantity) as total_qty'),
                \DB::raw('COUNT(DISTINCT order_items.order_id) as orders_count'),
                \DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('orders_count')
            ->get();

        // =====================
        // 📊 РОСТ
        // =====================
        $today = Carbon::today();

        $todayRevenue = $orders
            ->where('created_at', '>=', $today)
            ->sum(fn ($o) => $o->items->sum(fn ($i) => $i->price * $i->quantity)
            );

        $yesterdayRevenue = $orders
            ->whereBetween('created_at', [$today->copy()->subDay(), $today])
            ->sum(fn ($o) => $o->items->sum(fn ($i) => $i->price * $i->quantity)
            );

        $growth = $yesterdayRevenue > 0
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100
            : 0;

        // =====================
        // 📌 СТАТУСЫ
        // =====================
        $statusStats = $orders->groupBy('status')->map->count();

        return view('admin.dashboard', compact(
            'topProducts',
            'totalOrders',
            'totalRevenue',
            'avgOrder',
            'ordersByHour',
            'peakHour',
            'ordersByDay',
            'revenueByDay',
            'todayRevenue',
            'yesterdayRevenue',
            'growth',
            'statusStats',
           'frequentlyOrdered',
        ));
    }
}
