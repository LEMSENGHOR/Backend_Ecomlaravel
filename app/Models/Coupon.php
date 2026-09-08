<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'max_usage',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }
}
