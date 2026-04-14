<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Product::class) ?? false;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'sku' => ['required', 'string', 'max:60', Rule::unique('products', 'sku')->ignore($productId)],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:200', Rule::unique('products', 'slug')->ignore($productId)],
            'artist_id' => ['required', 'exists:artists,id'],
            'genre_id' => ['required', 'exists:genres,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'album_title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string'],
            'release_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'label_name' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:80'],
            'media_format' => ['required', 'string', 'max:40'],
            'disc_condition' => ['required', 'string', 'max:40'],
            'sleeve_condition' => ['required', 'string', 'max:40'],
            'rarity_level' => ['required', 'string', 'max:40'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'promotional_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', new Enum(ProductStatus::class)],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_rare' => ['sometimes', 'boolean'],
            'is_new_arrival' => ['sometimes', 'boolean'],
            'is_on_sale' => ['sometimes', 'boolean'],
            'is_best_seller' => ['sometimes', 'boolean'],
        ];
    }
}
