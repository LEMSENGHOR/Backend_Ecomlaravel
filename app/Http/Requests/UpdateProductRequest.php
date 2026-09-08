<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'integer', 'exists:sub_categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'sku' => [
                'nullable', 'string', 'max:100',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'INACTIVE', 'OUT_OF_STOCK'])],
        ];
    }
}
