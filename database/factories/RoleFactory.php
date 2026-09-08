<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = \App\Models\Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['ADMIN', 'USER', 'SUPPORT']),
        ];
    }
}
