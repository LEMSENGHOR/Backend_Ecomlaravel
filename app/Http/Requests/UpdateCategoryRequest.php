<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id ?? $this->route('category');

        return [
            'name' => [
                'sometimes', 'required', 'string', 'max:100',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }
}
