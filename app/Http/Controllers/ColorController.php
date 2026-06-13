<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::orderBy('id', 'desc')->get(); // все цвета, сортировка по ID

        return view('admin.color.index', compact('colors'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        Color::create($request->only('title'));

        return back()->with('success', 'Цвет добавлен');
    }

    public function update(Request $request, Color $color)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $color->update(['title' => $request->title]);

        return back()->with('success', 'Категория обновлена');
    }

public function toggle(Color $color)
{
    $color->update([
        'is_active' => !$color->is_active,
    ]);

    return back();
}
}
