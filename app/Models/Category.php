<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function matches()
    {
        return $this->belongsToMany(
            Category::class,
            'category_matches',
            'category_id',
            'matched_category_id'
        );
    }
}
