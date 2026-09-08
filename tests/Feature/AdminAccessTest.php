<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_regular_user_cannot_create_a_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/categories', [
            'name' => 'Electronics',
        ]);

        $response->assertForbidden();
    }

    public function test_an_admin_can_create_a_category(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::factory()->create(['name' => 'ADMIN']);
        $admin->roles()->attach($adminRole);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/categories', [
            'name' => 'Electronics',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    }

    public function test_a_guest_cannot_create_a_category(): void
    {
        $this->postJson('/api/categories', ['name' => 'Electronics'])
            ->assertUnauthorized();
    }

    public function test_an_admin_can_delete_a_category(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::factory()->create(['name' => 'ADMIN']));
        $category = Category::factory()->create();

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/categories/{$category->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
