<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use Illuminate\Http\Request;

class CategoryMatchController extends Controller
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
        Category $category
    ) {
        $category->matches()->sync(
            $request->matched_categories ?? []
        );

        return back()->with(
            'success',
            'Связи обновлены'
        );
    }

public function show () {

}


}
