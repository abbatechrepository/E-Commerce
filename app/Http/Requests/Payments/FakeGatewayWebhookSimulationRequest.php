<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class FakeGatewayWebhookSimulationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,declined,cancelled,refunded,pending,failed'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
