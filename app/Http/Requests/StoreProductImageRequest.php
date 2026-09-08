<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'required_without:image_url',
            ],

            'image_url' => [
                'nullable',
                'string',
                'url',
                'max:500',
                'required_without:image',
            ],

            'is_primary' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}