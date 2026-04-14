<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status?->value,
            'amount' => $this->amount,
            'method' => $this->method,
            'provider' => $this->provider,
            'external_reference' => $this->external_reference,
            'transactions' => $this->whenLoaded('transactions', function (): array {
                return $this->transactions->map(fn ($transaction): array => [
                    'transaction_code' => $transaction->transaction_code,
                    'status' => $transaction->status?->value,
                    'provider_response_code' => $transaction->provider_response_code,
                    'provider_message' => $transaction->provider_message,
                ])->all();
            }),
        ];
    }
}
