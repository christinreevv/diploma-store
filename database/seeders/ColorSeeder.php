<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['title' => 'Красный', 'code' => 'red'],
            ['title' => 'Бежевый', 'code' => 'beige'],
            ['title' => 'Синий', 'code' => 'navy'],
            ['title' => 'Белый', 'code' => 'white'],
            ['title' => 'Черный', 'code' => 'black'],
            ['title' => 'Зелёный', 'code' => 'green'],
            ['title' => 'Жёлтый', 'code' => 'yellow'],
            ['title' => 'Розовый', 'code' => 'pink'],
        ];

        foreach ($colors as $color) {
            Color::updateOrCreate(['code' => $color['code']], $color);
        }
    }
}
