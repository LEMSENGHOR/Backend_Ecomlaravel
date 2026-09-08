<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    // GET /api/cart
    public function show(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart($request->user());

        $cart->load('items.product');

        return response()->json([
            'message' => 'Cart retrieved successfully.',
            'cart' => $cart,
            'total' => $this->cartService->total($cart),
        ]);
    }

    // POST /api/cart/items
    public function storeItem(StoreCartItemRequest $request)
    {
        try {
            $item = $this->cartService->addItem(
                $request->user(),
                $request->product_id,
                $request->quantity
            );
        } catch (InsufficientStockException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'item' => $item->load('product'),
        ], 201);
    }

    // PATCH /api/cart/items/{cartItem}
    public function updateItem(
        UpdateCartItemRequest $request,
        int $cartItem
    ) {
        try {
            $item = $this->cartService->updateItemQuantity(
                $request->user(),
                $cartItem,
                $request->quantity
            );
        } catch (InsufficientStockException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Cart item updated successfully.',
            'item' => $item->load('product'),
        ]);
    }

    // DELETE /api/cart/items/{cartItem}
    public function destroyItem(
        Request $request,
        int $cartItem
    ) {
        $this->cartService->removeItem(
            $request->user(),
            $cartItem
        );

        return response()->json([
            'message' => 'Cart item removed successfully.',
        ]);
    }
}