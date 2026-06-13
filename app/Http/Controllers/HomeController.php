<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
public function index()
{
    $products = Product::with([
        'productColors.images',
        'sizes',
    ])->latest()->get();

    $limitedProduct = Product::with([
        'productColors.images',
    ])
        ->where('is_limited', 1)
        ->latest()
        ->first();

    $newProduct = Product::with([
        'productColors.images',
    ])
        ->where('is_limited', 0)
        ->latest()
        ->first();

    $popularIds = DB::table('order_items')
        ->select('product_id', DB::raw('SUM(quantity) as total_sales'))
        ->groupBy('product_id')
        ->orderByDesc('total_sales')
        ->limit(12)
        ->pluck('product_id');

    $popularIdsArray = $popularIds->toArray();

    $popularProducts = Product::with([
        'productColors.images',
        'sizes',
    ])
        ->whereIn('id', $popularIdsArray)
        ->get()
        ->sortBy(function ($product) use ($popularIdsArray) {
            return array_search($product->id, $popularIdsArray);
        });

    // Если заказов ещё нет
    if ($popularProducts->isEmpty()) {
        $popularProducts = Product::with([
            'productColors.images',
            'sizes',
        ])
            ->latest()
            ->take(12)
            ->get();
    }

    return view('welcome', compact(
        'products',
        'limitedProduct',
        'newProduct',
        'popularProducts'
    ));
}

    public function limited()
    {
        $products = Product::with([
            'productColors.images',
            'sizes',
        ])
            ->where('is_limited', 1)
            ->latest()
            ->paginate(12);

        return view('collections.limited.index', compact('products'));
    }

    public function newArrivals()
    {
        $products = Product::with([
            'productColors.images',
            'sizes',
        ])
            ->where('is_limited', 0)
            ->latest()
            ->paginate(12);

        return view('collections.new.index', compact('products'));
    }
}
