<?php

namespace App\Models;

use App\Models\ProductColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
     public $timestamps = false;

    protected $fillable = [
        'title',
        'code',
        'is_active'
    ];

    public function productColors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }
}
