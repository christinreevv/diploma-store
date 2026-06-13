<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'title',
        'slug',
    ];

  // App\Models\Size.php

public function products()
{
    return $this->belongsToMany(Product::class, 'product_sizes')
        ->withPivot(['price', 'stock'])
        ->withTimestamps();
}

}
