<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_listed_publicly(): void
    {
        Product::factory()->count(3)->create();

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_products_can_be_filtered_by_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        Product::factory()->create(['category_id' => $categoryA->id]);
        Product::factory()->count(2)->create(['category_id' => $categoryB->id]);

        $this->getJson("/api/products?category_id={$categoryA->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_products_can_be_searched_by_name(): void
    {
        Product::factory()->create(['name' => 'Wireless Mouse']);
        Product::factory()->create(['name' => 'Mechanical Keyboard']);

        $this->getJson('/api/products?search=Mouse')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Wireless Mouse');
    }

    public function test_a_single_product_can_be_viewed(): void
    {
        $product = Product::factory()->create();

        $this->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('id', $product->id);
    }

    public function test_creating_a_product_requires_admin(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'New Product',
            'price' => 19.99,
        ]);

        // No auth at all -> unauthenticated, not just forbidden
        $response->assertUnauthorized();
    }
}
