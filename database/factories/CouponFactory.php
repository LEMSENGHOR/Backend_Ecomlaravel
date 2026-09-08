<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = \App\Models\Coupon::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('COUPON-####'),
            'discount_type' => 'PERCENTAGE',
            'discount_value' => 10,
            'minimum_amount' => 0,
            'max_usage' => 0,
            'used_count' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'ACTIVE',
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'start_date' => now()->subMonth(),
            'end_date' => now()->subDay(),
        ]);
    }

    public function fixed(float $amount): static
    {
        return $this->state(fn () => [
            'discount_type' => 'FIXED',
            'discount_value' => $amount,
        ]);
    }
}
