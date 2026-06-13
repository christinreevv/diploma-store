<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
   public function index()
{
    $favorites = auth()->user()
        ->favorites()
        ->with([
            'product',
            'product.productColors.images',
            'product.sizes',
            'productColor',
        ])
        ->paginate(12);

    return view('favorites.index', compact('favorites'));
}

   public function toggle(ProductColor $productColor)
{
    $user = auth()->user();

    $favorite = $user->favorites()
        ->where('product_color_id', $productColor->id)
        ->first();

    if ($favorite) {
        $favorite->delete();
    } else {
        $user->favorites()->create([
            'product_id' => $productColor->product_id,
            'product_color_id' => $productColor->id,
        ]);
    }

    return back();
}
}
