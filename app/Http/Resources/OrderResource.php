<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status?->value,
            'subtotal' => $this->subtotal,
            'discount_total' => $this->discount_total,
            'shipping_total' => $this->shipping_total,
            'total' => $this->total,
            'placed_at' => $this->placed_at,
            'paid_at' => $this->paid_at,
            'shipment' => $this->whenLoaded('shipment', fn (): array => [
                'carrier' => $this->shipment?->carrier,
                'service_name' => $this->shipment?->service_name,
                'status' => $this->shipment?->status?->value,
                'tracking_code' => $this->shipment?->tracking_code,
            ]),
            'payment' => $this->whenLoaded('payment', fn (): array => [
                'status' => $this->payment?->status?->value,
                'provider' => $this->payment?->provider,
                'method' => $this->payment?->method,
            ]),
            'items' => $this->whenLoaded('items', function (): array {
                return $this->items->map(fn ($item): array => [
                    'product_name' => $item->product_name_snapshot,
                    'artist_name' => $item->artist_name_snapshot,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ])->all();
            }),
        ];
    }
}
