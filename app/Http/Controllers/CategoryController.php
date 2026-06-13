<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->get(); // все категории, сортировка по ID

        return view('admin.category.index', compact('categories'));
    }

   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255'
    ]);

    Category::create([
        'title' => $request->title,
        'slug' => Str::slug($request->title),
    ]);

    return back()->with('success', 'Категория добавлена');
}

   public function update(Request $request, Category $category)
{
    $request->validate([
        'title' => 'required|string|max:255'
    ]);

    $category->update([
        'title' => $request->title,
        'slug' => Str::slug($request->title),
    ]);

    return back()->with('success', 'Категория обновлена');
}

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('success', 'Категория удалена');
    }
}
