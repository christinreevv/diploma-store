<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorMatchController extends Controller
{
    public function index()
    {
        $categories = Category::with('matches')->get();

        $colors = Color::with('matches')->get();

        return view(
            'admin.category-matches.index',
            compact(
                'categories',
                'colors'
            )
        );
    }

    public function update(
        Request $request,
        Color $color
    ) {
        $color->matches()->sync(
            $request->matched_colors ?? []
        );

        return back()->with(
            'success',
            'Цветовые связи обновлены'
        );
    }
}
