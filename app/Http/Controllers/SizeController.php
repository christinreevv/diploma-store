<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::orderBy('id', 'desc')->get();

        return view('admin.size.index', compact('sizes'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        Size::create($request->only('title'));

        return back()->with('success', 'Размер добавлен');
    }

    public function update(Request $request, Size $size)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $size->update(['title' => $request->title]);

        return back()->with('success', 'Категория обновлена');
    }

    public function destroy(Size $size)
    {
        $size->delete();

        return back()->with('success', 'Категория удалена');
    }
}
