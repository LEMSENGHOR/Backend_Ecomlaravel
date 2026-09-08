<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'integer', 'exists:sub_categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'INACTIVE', 'OUT_OF_STOCK'])],
        ];
    }
}
