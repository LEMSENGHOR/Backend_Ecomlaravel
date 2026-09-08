<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_review_a_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Great product!',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'product_id' => $product->id]);
    }

    public function test_a_user_cannot_review_the_same_product_twice(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Review::create(['user_id' => $user->id, 'product_id' => $product->id, 'rating' => 4]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'product_id' => $product->id,
            'rating' => 5,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('product_id');
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'product_id' => $product->id,
            'rating' => 7,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('rating');
    }

    public function test_the_author_can_update_their_review(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()
            ->for($user)
            ->for(Product::factory())
            ->create(['rating' => 3]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/reviews/{$review->id}", ['rating' => 5]);

        $response->assertOk();
        $this->assertEquals(5, $review->fresh()->rating);
    }

    public function test_another_user_cannot_update_someone_elses_review(): void
    {
        $author = User::factory()->create();
        $intruder = User::factory()->create();
        $review = Review::factory()
            ->for($author)
            ->for(Product::factory())
            ->create();

        $response = $this->actingAs($intruder, 'sanctum')
            ->patchJson("/api/reviews/{$review->id}", ['rating' => 1]);

        $response->assertForbidden();
    }
}
