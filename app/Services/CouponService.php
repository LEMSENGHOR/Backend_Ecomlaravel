<?php

namespace App\Services;

use App\Exceptions\InvalidCouponException;
use App\Models\Coupon;
use Illuminate\Support\Carbon;

class CouponService
{
    /**
     * Look up a coupon by code and make sure it's actually usable right now.
     * Throws InvalidCouponException with a specific reason if not.
     */
    public function validate(string $code, float $subtotal): Coupon
    {
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            throw new InvalidCouponException('This coupon code does not exist.');
        }

        if ($coupon->status !== 'ACTIVE') {
            throw new InvalidCouponException('This coupon is no longer active.');
        }

        $now = Carbon::now();

        if ($now->lt($coupon->start_date) || $now->gt($coupon->end_date)) {
            throw new InvalidCouponException('This coupon is not valid at this time.');
        }

        if ($coupon->max_usage > 0 && $coupon->used_count >= $coupon->max_usage) {
            throw new InvalidCouponException('This coupon has reached its usage limit.');
        }

        if ($subtotal < $coupon->minimum_amount) {
            throw new InvalidCouponException(
                "This coupon requires a minimum order of {$coupon->minimum_amount}."
            );
        }

        return $coupon;
    }

    /**
     * Compute the discount amount for a subtotal, capped so it never exceeds the subtotal.
     */
    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = $coupon->discount_type === 'PERCENTAGE'
            ? $subtotal * ($coupon->discount_value / 100)
            : $coupon->discount_value;

        return round(min($discount, $subtotal), 2);
    }

    public function redeem(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}
