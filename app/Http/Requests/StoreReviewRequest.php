<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required', 'integer', 'exists:products,id',
                Rule::unique('reviews', 'product_id')->where('user_id', $this->user()?->id),
            ],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
