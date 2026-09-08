<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'discount_type' => ['required', Rule::in(['PERCENTAGE', 'FIXED'])],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'minimum_amount' => ['sometimes', 'numeric', 'min:0'],
            'max_usage' => ['sometimes', 'integer', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'INACTIVE', 'EXPIRED'])],
        ];
    }
}
