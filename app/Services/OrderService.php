<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
    ) {
    }

    /**
     * Turn a user's cart into an order: locks stock, decrements it, writes
     * order + order_items, optionally applies a coupon, then empties the cart.
     * Everything happens in one transaction so a stock failure rolls back cleanly.
     */
    public function checkoutFromCart(User $user, string $shippingAddress, ?string $couponCode = null): Order
    {
        $cart = $this->cartService->getOrCreateCart($user);
        $items = $cart->items()->with('product')->get();

        if ($items->isEmpty()) {
            throw new RuntimeException('Cannot check out an empty cart.');
        }

        return DB::transaction(function () use ($user, $shippingAddress, $couponCode, $cart, $items) {
            $subtotal = 0;

            // Lock product rows for update so concurrent checkouts can't oversell stock.
            foreach ($items as $item) {
                $product = $item->product()->lockForUpdate()->first();

                if ($item->quantity > $product->stock) {
                    throw new InsufficientStockException($product->name, $product->stock, $item->quantity);
                }

                $subtotal += $product->price * $item->quantity;
            }

            $discount = 0;
            $coupon = null;

            if ($couponCode) {
                $coupon = $this->couponService->validate($couponCode, $subtotal);
                $discount = $this->couponService->calculateDiscount($coupon, $subtotal);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'total_amount' => $subtotal - $discount,
                'status' => 'PENDING',
                'shipping_address' => $shippingAddress,
            ]);

            foreach ($items as $item) {
                $product = $item->product;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                    'subtotal' => $product->price * $item->quantity,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            if ($coupon) {
                $this->couponService->redeem($coupon);
            }

            $this->cartService->clear($cart);

            return $order->fresh('items');
        });
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order;
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
