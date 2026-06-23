<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // =========================================================
    // LIST
    // =========================================================

    public function index()
    {
        $products = Product::with([
            'category',
            'images',
            'sizes',
        ])
            ->latest()
            ->paginate(10);

        // Общая выручка
        $totalRevenue = OrderItem::sum(
            DB::raw('price * quantity')
        );

        // Всего заказов
        $totalOrders = Order::count();

        // Продано товаров
        $totalSold = OrderItem::sum('quantity');

        // Топ товаров
        $topProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total')
        )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $topColors = OrderItem::select(
            'product_color_id',
            DB::raw('SUM(quantity) as total')
        )
            ->with('productColor.color')
            ->groupBy('product_color_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('admin.products.index', compact(
            'products',
            'totalRevenue',
            'totalOrders',
            'totalSold',
            'topProducts',
            'topColors'
        ));
    }

    // =========================================================
    // CREATE
    // =========================================================

    public function create()
    {
        $categories = Category::all();
        $sizes = Size::all();
        $colors = Color::all();
        $allProducts = Product::with(['category', 'sizes', 'productColors.color', 'images'])->get();

        return view('admin.products.create', compact('categories', 'sizes', 'colors', 'allProducts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'care_instructions' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'sizes' => 'array',
            'sizes.*' => 'exists:sizes,id',
            'colors' => 'array',
            'colors.*' => 'exists:colors,id',
            'color_images' => 'array',
            'color_images.*.*' => 'file|image|max:2048',
            'images' => 'array',
            'images.*' => 'file|image|max:2048',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        // Генерация slug
        $slug = Str::slug($data['title']);
        $count = Product::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-'.($count + 1);
        }

        // Создание продукта
        $product = Product::create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'care_instructions' => $data['care_instructions'] ?? null,
            'category_id' => $data['category_id'],
            'is_active' => $data['is_active'] ?? 1,
            'is_limited' => $request->boolean('is_limited'),
        ]);

        if (! empty($data['sizes'])) {

            $attachData = [];

            foreach ($data['sizes'] as $sizeId) {
                $attachData[$sizeId] = [
                    'price' => $data['price'] ?? 0,
                    'stock' => $data['stock'] ?? 0,
                ];
            }

            $product->sizes()->sync($attachData);

        } else {

            // универсальный размер
            $defaultSize = Size::firstOrCreate([
                'title' => 'ONE SIZE',
            ]);

            $product->sizes()->sync([
                $defaultSize->id => [
                    'price' => $data['price'] ?? 0,
                    'stock' => $data['stock'] ?? 0,
                ],
            ]);
        }

        // --- Привязка цветов и изображений ---
        if (! empty($data['colors'])) {
            foreach ($data['colors'] as $colorId) {
                $productColor = $product->productColors()->create([
                    'color_id' => $colorId,
                ]);

                if (! empty($data['color_images'][$colorId])) {
                    foreach ($data['color_images'][$colorId] as $file) {
                        $path = $file->store('products/colors', 'public');
                        $productColor->images()->create([
                            'path' => $path,
                            'is_main' => true,
                            'sort_order' => 0,
                        ]);
                    }
                }
            }
        }

        // --- Общие изображения продукта ---
        if (! empty($data['images'])) {
            foreach ($data['images'] as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'is_main' => false,
                    'sort_order' => 0,
                ]);
            }
        }

        return redirect()->route('admin.products.create')
            ->with('success', 'Продукт успешно создан!');
    }

    // =========================================================
    // EDIT
    // =========================================================

    public function edit(Product $product)
    {
        $product->load([
            'productColors.color',
            'productColors.images',
            'sizes',
            'images',
        ]);

        $categories = Category::all();
        $sizes = Size::all();
        $colors = Color::all();

        $price = $product->sizes->first()?->pivot->price ?? 0;
        $stock = $product->sizes->first()?->pivot->stock ?? 0;

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'sizes',
            'colors',
            'price',
            'stock'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'care_instructions' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'sizes' => 'array',
            'sizes.*' => 'exists:sizes,id',
            'colors' => 'array',
            'colors.*' => 'exists:colors,id',
            'color_images' => 'array',
            'color_images.*.*' => 'file|image|max:2048',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        // ================= PRODUCT =================
        $product->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'care_instructions' => $data['care_instructions'] ?? null,
            'category_id' => $data['category_id'],
            'is_active' => $data['is_active'] ?? 1,
            'is_limited' => $request->boolean('is_limited'),
        ]);

        // ================= DELETE IMAGES =================
        if ($request->filled('deleted_images')) {

            $deleted = json_decode($request->deleted_images, true) ?? [];

            $images = \App\Models\ProductImage::whereIn('id', $deleted)->get();

            foreach ($images as $img) {

                Storage::disk('public')->delete($img->path);

                $productColor = $img->productColor;

                $img->delete();

                if ($productColor && $productColor->images()->count() === 0) {
                    $productColor->delete();
                }
            }
        }

        // ================= SIZES =================
        if (! empty($data['sizes'])) {
            $attachData = [];

            foreach ($data['sizes'] as $sizeId) {
                $attachData[$sizeId] = [
                    'price' => $data['price'] ?? 0,
                    'stock' => $data['stock'] ?? 0,
                ];
            }

            $product->sizes()->sync($attachData);
        }

        // ================= COLORS =================
        if (! empty($data['colors'])) {

            foreach ($data['colors'] as $colorId) {

                $productColor = \App\Models\ProductColor::firstOrCreate([
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                ]);

                $files = $request->file("color_images.$colorId", []);

                if (! is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $image) {

                    if (! $image) {
                        continue;
                    }

                    $path = $image->store('products/colors', 'public');

                    $productColor->images()->create([
                        'path' => $path,
                        'is_main' => $productColor->images()->count() === 0,
                        'sort_order' => ($productColor->images()->max('sort_order') ?? 0) + 1,
                    ]);
                }
            }
        }

        // ================= REMOVE UNSELECTED COLORS (SAFE) =================
        $selectedColors = $data['colors'] ?? [];

        $product->productColors()
            ->whereNotIn('color_id', $selectedColors)
            ->with('images')
            ->get()
            ->each(function ($pc) {

                foreach ($pc->images as $img) {
                    Storage::disk('public')->delete($img->path);
                }

                $pc->images()->delete();
                $pc->delete();
            });

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('success', 'Продукт успешно обновлен!');
    }

    // =========================================================
    // SHOW
    // =========================================================

    public function show(Request $request, $slug, $color = null)
    {
        $product = Product::with([
            'sizes',
            'productColors.color',
            'productColors.images',
            'images',
            'category.matches',
        ])->where('slug', $slug)->firstOrFail();

        $color = $color ?: $request->route('color');

        $selectedColor = null;

        if ($color) {
            $selectedColor = $product->productColors
                ->first(function ($productColor) use ($color) {
                    return $productColor->color
                        && \Illuminate\Support\Str::slug($productColor->color->title) === $color;
                });
        }

        if (! $selectedColor) {
            $selectedColor = $product->productColors->first();
        }

        $defaultImage = $selectedColor?->images
            ->firstWhere('is_main', true);

        if (! $defaultImage) {
            $defaultImage = $selectedColor?->images->first();
        }

        $matchedCategoryIds = $product->category?->matches
            ->pluck('id')
            ->toArray() ?? [];

        $recommendedProducts = Product::with([
            'sizes',
            'productColors.images',
            'productColors.color',
        ])
            ->whereIn('category_id', $matchedCategoryIds)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(12)
            ->get();

        // -----------------------------
        // CART STATE (ВАЖНО)
        // -----------------------------
        $inCart = false;

        if (auth()->check() && $selectedColor) {

            $cart = \App\Models\Cart::where('user_id', auth()->id())->first();

            if ($cart) {
                $inCart = $cart->items()
                    ->where('product_id', $product->id)
                    ->where('product_color_id', $selectedColor->id)
                    ->exists();
            }
        }

        return view('products.show', [
            'product' => $product,
            'defaultImage' => $defaultImage,
            'selectedColorKey' => \Illuminate\Support\Str::slug($selectedColor?->color?->title),
            'selectedColor' => $selectedColor,
            'recommendedProducts' => $recommendedProducts,
            'inCart' => $inCart,
        ]);
    }
    // =========================================================
    // DELETE
    // =========================================================

    public function destroy(Product $product)
    {
        // 1. удалить избранное (ВАЖНО: по product_id, а не relation)
        \App\Models\Favorite::where('product_id', $product->id)->delete();

        // 2. удалить размеры
        $product->sizes()->detach();

        // 3. удалить картинки цветов
        foreach ($product->productColors as $productColor) {
            $productColor->images()->delete();
        }

        // 4. удалить связи цветов
        $product->productColors()->delete();

        // 5. удалить изображения продукта
        $product->images()->delete();

        // 6. удалить сам продукт
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Товар удалён');
    }

    // =========================================================
    // COLOR STATUS
    // =========================================================

    public function toggle(Color $color)
    {
        $color->update([
            'is_active' => ! $color->is_active,
        ]);

        return back();
    }

    // =========================================================
    // STATUS
    // =========================================================

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return back();
    }
}
