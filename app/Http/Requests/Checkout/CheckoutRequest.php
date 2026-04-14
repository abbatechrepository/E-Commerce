<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'cart_id' => ['required', 'exists:carts,id'],
            'address_id' => ['required', 'exists:addresses,id'],
            'payment_method' => ['required', 'string', 'max:40'],
            'shipping_service' => ['required', 'string', 'max:80'],
            'coupon_code' => ['nullable', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
