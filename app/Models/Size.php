<?php

namespace App\Models;

use App\Models\ProductSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
   public $timestamps = false;

    protected $fillable = [
        'title',
    ];

    public function productSizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }
}
