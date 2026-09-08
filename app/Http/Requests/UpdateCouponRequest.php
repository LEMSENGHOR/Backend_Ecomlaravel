<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')?->id ?? $this->route('coupon');

        return [
            'code' => [
                'sometimes', 'required', 'string', 'max:50',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'discount_type' => ['sometimes', 'required', Rule::in(['PERCENTAGE', 'FIXED'])],
            'discount_value' => ['sometimes', 'required', 'numeric', 'min:0'],
            'minimum_amount' => ['sometimes', 'numeric', 'min:0'],
            'max_usage' => ['sometimes', 'integer', 'min:0'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after:start_date'],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'INACTIVE', 'EXPIRED'])],
        ];
    }
}
