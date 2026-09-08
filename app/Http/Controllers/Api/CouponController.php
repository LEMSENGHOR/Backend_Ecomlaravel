<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidCouponException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponService $couponService
    ) {
    }

    // GET /api/coupons
    public function index()
    {
        return response()->json([
            'message' => 'Coupons retrieved successfully.',
            'coupons' => Coupon::query()->paginate(20),
        ]);
    }

    // POST /api/coupons
    public function store(StoreCouponRequest $request)
    {
        $coupon = Coupon::create($request->validated());

        return response()->json([
            'message' => 'Coupon created successfully.',
            'coupon' => $coupon,
        ], 201);
    }

    // PATCH /api/coupons/{coupon}
    public function update(
        UpdateCouponRequest $request,
        Coupon $coupon
    ) {
        $coupon->update($request->validated());

        return response()->json([
            'message' => 'Coupon updated successfully.',
            'coupon' => $coupon->fresh(),
        ]);
    }

    // DELETE /api/coupons/{coupon}
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json([
            'message' => 'Coupon deleted successfully.',
        ]);
    }

    // POST /api/coupons/check
    public function check(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $coupon = $this->couponService->validate(
                $request->code,
                $request->subtotal
            );
        } catch (InvalidCouponException $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $discount = $this->couponService->calculateDiscount(
            $coupon,
            $request->subtotal
        );

        return response()->json([
            'valid' => true,
            'message' => 'Coupon is valid.',
            'coupon' => $coupon,
            'discount' => $discount,
            'subtotal' => $request->subtotal,
            'total_after_discount' => $request->subtotal - $discount,
        ]);
    }
}