<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['title' => 'Куртки', 'slug' => 'jackets'],
            ['title' => 'Пальто', 'slug' => 'coats'],
            ['title' => 'Рубашки', 'slug' => 'shirts'],
            ['title' => 'Трикотаж', 'slug' => 'knitwear'],
            ['title' => 'Блейзеры', 'slug' => 'blazers'],
            ['title' => 'Топы | Боди', 'slug' => 'tops-bodies'],
            ['title' => 'Кардиганы | Свитера', 'slug' => 'cardigans-sweaters'],
            ['title' => 'Платья | Комбинезоны', 'slug' => 'dresses-jumpsuits'],
            ['title' => 'Джинсы', 'slug' => 'jeans'],
            ['title' => 'Брюки', 'slug' => 'pants'],
            ['title' => 'Юбки', 'slug' => 'skirts'],
            ['title' => 'Обувь', 'slug' => 'shoes'],
            ['title' => 'Сумки', 'slug' => 'bags'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
