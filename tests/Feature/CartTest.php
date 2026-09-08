<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_a_product_creates_a_cart_and_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_more_than_available_stock_is_rejected(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id]);
    }

    public function test_adding_the_same_product_twice_increases_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_a_cart_item_can_be_removed(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $cartItemId = \App\Models\CartItem::first()->id;

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/cart/items/{$cartItemId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItemId]);
    }

    public function test_a_users_cart_total_reflects_item_prices(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 10.00, 'stock' => 10]);

        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/cart');

        $response->assertOk()->assertJsonPath('total', 30);
    }
}
