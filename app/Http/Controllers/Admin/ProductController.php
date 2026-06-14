<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
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

        return view('admin.products.index', compact('products'));
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

        $product->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'care_instructions' => $data['care_instructions'] ?? null,
            'category_id' => $data['category_id'],
            'is_active' => $data['is_active'] ?? 1,
            'is_limited' => $request->boolean('is_limited'),
        ]);

        // ---------------- DELETE IMAGES ----------------
        if ($request->filled('deleted_images')) {

            $deleted = json_decode($request->deleted_images, true);

            if (! empty($deleted)) {

                $images = \App\Models\ProductImage::whereIn('id', $deleted)->get();

                foreach ($images as $img) {

                    Storage::disk('public')->delete($img->path);
                    $img->delete();

                    // после удаления сразу проверяем цвет
                    $productColor = $img->productColor;

                    if ($productColor && $productColor->images()->count() === 0) {
                        $productColor->delete();
                    }
                }
            }
        }

        $productColors = $product->productColors()->with('images')->get();

        foreach ($productColors as $productColor) {

            // если у цвета нет изображений — удаляем связь
            if ($productColor->images->count() === 0) {

                $productColor->delete();
            }
        }

        // Обновление размеров
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

        // Обновление цветов
        if (! empty($data['colors'])) {

            foreach ($data['colors'] as $colorId) {

                // получаем или создаём связку товара и цвета
                $productColor = \App\Models\ProductColor::firstOrCreate([
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                ]);

                // ЕСЛИ ЗАГРУЖЕНЫ НОВЫЕ ФОТО
                if ($request->hasFile("color_images.$colorId")) {

                    // удалить старые фото
                    foreach ($productColor->images as $oldImage) {

                        Storage::disk('public')->delete($oldImage->path);

                        $oldImage->delete();
                    }

                    // сохранить новые
                    foreach ($request->file("color_images.$colorId") as $image) {

                        $path = $image->store('products/colors', 'public');

                        $productColor->images()->create([
                            'path' => $path,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.products.edit', $product->id)
            ->with('success', 'Продукт успешно обновлен!');
    }

    // =========================================================
    // SHOW
    // =========================================================

    public function show($slug)
    {
        $product = Product::with([
            'sizes',
            'productColors.color',
            'productColors.images',
            'images',
            'category.matches',
        ])->where('slug', $slug)->firstOrFail();

        // Дефолтное изображение
        $defaultImage = $product->images->where('is_main', true)->first();

        if (! $defaultImage && $product->images->count()) {
            $defaultImage = $product->images->first();
        }

        // категории для блока "Покупают вместе"
        $matchedCategoryIds = $product->category
            ?->matches
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

        return view('products.show', [
            'product' => $product,
            'defaultImage' => $defaultImage ? $defaultImage->path : null,
            'selectedColorKey' => $product->productColors->first()?->color->key ?? null,
            'recommendedProducts' => $recommendedProducts,
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
