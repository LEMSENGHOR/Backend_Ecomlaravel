<?php

namespace Tests\Feature;

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_coupon_returns_the_computed_discount(): void
    {
        $coupon = Coupon::factory()->create(['discount_type' => 'FIXED', 'discount_value' => 15]);

        $response = $this->postJson('/api/coupons/check', [
            'code' => $coupon->code,
            'subtotal' => 100,
        ]);

        $response->assertOk()
            ->assertJson(['valid' => true, 'discount' => 15]);
    }

    public function test_an_unknown_coupon_code_is_invalid(): void
    {
        $response = $this->postJson('/api/coupons/check', [
            'code' => 'DOES-NOT-EXIST',
            'subtotal' => 100,
        ]);

        $response->assertUnprocessable()->assertJson(['valid' => false]);
    }

    public function test_a_coupon_below_minimum_amount_is_invalid(): void
    {
        $coupon = Coupon::factory()->create(['minimum_amount' => 200]);

        $response = $this->postJson('/api/coupons/check', [
            'code' => $coupon->code,
            'subtotal' => 50,
        ]);

        $response->assertUnprocessable()->assertJson(['valid' => false]);
    }

    public function test_a_fixed_discount_never_exceeds_the_subtotal(): void
    {
        $coupon = Coupon::factory()->create(['discount_type' => 'FIXED', 'discount_value' => 500]);

        $response = $this->postJson('/api/coupons/check', [
            'code' => $coupon->code,
            'subtotal' => 30,
        ]);

        $response->assertOk()->assertJson(['discount' => 30]);
    }
}
