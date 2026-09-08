<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(
        private readonly WishlistService $wishlistService
    ) {
    }

    // GET /api/wishlist
    public function index(Request $request)
    {
        $wishlists = $request->user()
            ->wishlists()
            ->with('product')
            ->paginate(20);

        return response()->json([
            'message' => 'Wishlist retrieved successfully.',
            'wishlists' => $wishlists,
        ]);
    }

    // POST /api/wishlist/toggle
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id'
            ],
        ]);

        $added = $this->wishlistService->toggle(
            $request->user(),
            $request->product_id
        );

        return response()->json([
            'message' => $added
                ? 'Product added to wishlist successfully.'
                : 'Product removed from wishlist successfully.',
            'added' => $added,
        ]);
    }
}