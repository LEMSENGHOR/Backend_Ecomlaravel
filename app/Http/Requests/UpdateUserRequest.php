<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => [
                'sometimes', 'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['sometimes', 'required', Rule::in(['ACTIVE', 'INACTIVE', 'SUSPENDED'])],
        ];
    }
}
