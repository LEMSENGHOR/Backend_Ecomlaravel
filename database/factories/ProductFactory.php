<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 500),
            'stock' => fake()->numberBetween(5, 100),
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'status' => 'ACTIVE',
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }
}
