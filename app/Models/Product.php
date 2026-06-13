<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Color;
use App\Models\Favorite;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'care_instructions',
        'category_id',
        'is_active',
        'is_limited'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
public function colors()
{
    return $this->belongsToMany(
        Color::class,
        'product_colors', // Имя таблицы
        'product_id',     // FK на Product
        'color_id'        // FK на Color
    )->withTimestamps();
}


    // App\Models\Product.php

public function sizes()
{
    return $this->belongsToMany(Size::class, 'product_sizes') // <-- здесь имя таблицы
                ->withPivot(['price', 'stock'])
                ->withTimestamps();
}


 public function productColors()
    {
        return $this->hasMany(ProductColor::class);
    }

public function images()
{
    // получаем все изображения через ProductColor
    return $this->hasManyThrough(
        ProductImage::class,   // конечная модель
        ProductColor::class,   // промежуточная модель
        'product_id',          // foreign key в таблице product_colors
        'product_color_id',    // foreign key в таблице product_images
        'id',                  // локальный ключ продукта
        'id'                   // локальный ключ product_colors
    );
}


    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }
}
