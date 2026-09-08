<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

class CartService
{
    /**
     * Get the user's cart, creating one if it doesn't exist yet.
     */
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * Add a product to the cart, or increase quantity if it's already there.
     */
    public function addItem(User $user, int $productId, int $quantity): CartItem
    {
        $product = Product::findOrFail($productId);
        $cart = $this->getOrCreateCart($user);

        $item = $cart->items()->where('product_id', $productId)->first();
        $newQuantity = ($item?->quantity ?? 0) + $quantity;

        $this->assertInStock($product, $newQuantity);

        return CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $productId],
            ['quantity' => $newQuantity]
        );
    }

    /**
     * Set a cart item's quantity to an exact value.
     */
    public function updateItemQuantity(User $user, int $cartItemId, int $quantity): CartItem
    {
        $cart = $this->getOrCreateCart($user);
        $item = $cart->items()->findOrFail($cartItemId);

        $this->assertInStock($item->product, $quantity);

        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function removeItem(User $user, int $cartItemId): void
    {
        $cart = $this->getOrCreateCart($user);
        $cart->items()->findOrFail($cartItemId)->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    /**
     * Sum of (product price x quantity) across all items in the cart.
     */
    public function total(Cart $cart): float
    {
        return $cart->items()
            ->with('product:id,price')
            ->get()
            ->sum(fn (CartItem $item) => $item->product->price * $item->quantity);
    }

    private function assertInStock(Product $product, int $requestedQuantity): void
    {
        if ($requestedQuantity > $product->stock) {
            throw new InsufficientStockException($product->name, $product->stock, $requestedQuantity);
        }
    }
}
