<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wishlist;

class WishlistService
{
    /**
     * Add the product if it's not already wishlisted, remove it if it is.
     * Returns true if the product ended up added, false if removed.
     */
    public function toggle(User $user, int $productId): bool
    {
        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        return true;
    }
}
