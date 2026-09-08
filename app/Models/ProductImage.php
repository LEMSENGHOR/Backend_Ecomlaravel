<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_url',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // product_images table does not have updated_at
    const UPDATED_AT = null;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}