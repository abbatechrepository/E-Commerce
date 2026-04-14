<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'max:5120'],
            'alt_text' => ['nullable', 'string', 'max:180'],
            'is_primary' => ['sometimes', 'boolean'],
        ];
    }
}
