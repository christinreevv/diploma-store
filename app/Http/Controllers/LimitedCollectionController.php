<?php

namespace App\Http\Controllers;

use App\Models\Product;

class LimitedCollectionController extends Controller
{
public function index()
{
   $limitedProduct = Product::where('is_limited', 1)
    ->latest()
    ->first();

    return view('welcome', compact('limitedProduct'));
}
}
