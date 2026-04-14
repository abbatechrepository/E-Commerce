<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class FakeGatewayTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_id' => ['required', 'exists:payments,id'],
            'order_number' => ['required', 'string', 'max:40'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'simulate_status' => ['nullable', 'in:pending,approved,declined'],
            'callback_url' => ['nullable', 'url'],
        ];
    }
}
