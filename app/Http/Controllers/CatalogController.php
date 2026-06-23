<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('is_active', 1)
            ->with([
                'category',
                'sizes',
                'productColors.color',
                'productColors.images',
            ])
            ->addSelect([
                'min_price' => ProductSize::selectRaw('MIN(price)')
                    ->whereColumn('product_id', 'products.id'),
            ]);

        // фильтры
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

        // suggestions
        if ($request->ajax() && $request->filled('suggest') && $request->filled('q')) {
            $products = Product::where('is_active', 1)
                ->where('title', 'like', '%'.$request->q.'%')
                ->limit(5)
                ->get();

            return view('catalog._suggestions', compact('products'));
        }

        // -------------------------
        // SORTING (РАБОЧАЯ ВЕРСИЯ)
        // -------------------------
        switch ($request->get('sort')) {

            case 'price_asc':
                $query->orderBy('min_price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('min_price', 'desc');
                break;

            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;

            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;

            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;

            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('catalog.index', [
            'products' => $products,
            'categories' => Category::all(),
            'colors' => Color::all(),
        ]);
    }
}
