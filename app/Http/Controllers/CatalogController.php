<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', 1)
            ->with([
                'category',
                'sizes',
                'productColors.color',
                'productColors.images',
            ]);

        if ($request->filled('category_id')) {
            $query->whereIn('category_id', $request->category_id);
        }

        if ($request->filled('color')) {
            $query->whereHas('productColors.color', function ($q) use ($request) {
                $q->whereIn('code', $request->color);
            });
        }

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // suggestions
        if ($request->ajax() && $request->filled('suggest') && $request->filled('q')) {

            $products = Product::where('is_active', 1)
                ->where('title', 'like', '%'.$request->q.'%')
                ->limit(5)
                ->get();

            return view('catalog._suggestions', compact('products'));
        }

        $products = $query->paginate(12);

        $categories = Category::all();
        $colors = Color::all();
        

        return view('catalog.index', compact(
            'products',
            'categories',
            'colors'
        ));
    }
}
