<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'quantity',
        'description',
        'image',
        'slug',
        'is_featured',
        'is_active',
    ];
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
