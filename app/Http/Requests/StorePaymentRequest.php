<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id', 'unique:payments,order_id'],
            'transaction_id' => ['nullable', 'string', 'max:100', 'unique:payments,transaction_id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:30'],
            'status' => ['sometimes', Rule::in(['PENDING', 'COMPLETED', 'FAILED', 'REFUNDED'])],
        ];
    }
}
