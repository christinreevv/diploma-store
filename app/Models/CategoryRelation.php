<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryRelation extends Model
{
    protected $fillable = [
        'category_id',
        'related_category_id',
    ];
}
