<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_checking_out_creates_an_order_and_decrements_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 20, 'stock' => 10]);

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'shipping_address' => '123 Main St',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 40,
            'status' => 'PENDING',
        ]);
        $this->assertEquals(8, $product->fresh()->stock);
    }

    public function test_checking_out_empties_the_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'shipping_address' => '123 Main St',
        ]);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checking_out_an_empty_cart_fails(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'shipping_address' => '123 Main St',
        ]);

        $response->assertBadRequest();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checking_out_rolls_back_if_stock_ran_out_after_adding_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        // Someone else buys the remaining stock in the meantime.
        $product->update(['stock' => 0]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'shipping_address' => '123 Main St',
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('cart_items', 1); // cart was NOT cleared
    }

    public function test_checking_out_with_a_valid_coupon_applies_the_discount(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);
        $coupon = Coupon::factory()->create(['discount_type' => 'PERCENTAGE', 'discount_value' => 10]);

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'shipping_address' => '123 Main St',
            'coupon_code' => $coupon->code,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('orders', ['total_amount' => 90]);
        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    public function test_checking_out_with_an_expired_coupon_fails(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);
        $coupon = Coupon::factory()->expired()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'shipping_address' => '123 Main St',
            'coupon_code' => $coupon->code,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_a_user_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($owner, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $this->actingAs($owner, 'sanctum')->postJson('/api/orders', [
            'shipping_address' => '123 Main St',
        ]);

        $order = $owner->orders()->first();

        $this->actingAs($intruder, 'sanctum')
            ->getJson("/api/orders/{$order->id}")
            ->assertForbidden();
    }
}
