<?php

namespace App\Application\Cart;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\Customer;

class GetOrCreateCartAction
{
    public function execute(?Customer $customer, string $sessionId): Cart
    {
        $query = Cart::query()->where('status', CartStatus::ACTIVE);

        if ($customer) {
            $cart = $query->where('customer_id', $customer->id)->latest()->first();

            if ($cart) {
                if ($cart->session_id !== $sessionId) {
                    $cart->update(['session_id' => $sessionId]);
                }

                return $cart;
            }
        }

        $guestCart = Cart::query()
            ->where('status', CartStatus::ACTIVE)
            ->where('session_id', $sessionId)
            ->latest()
            ->first();

        if ($guestCart) {
            if ($customer && $guestCart->customer_id !== $customer->id) {
                $guestCart->update(['customer_id' => $customer->id]);
            }

            return $guestCart;
        }

        return Cart::query()->create([
            'customer_id' => $customer?->id,
            'session_id' => $sessionId,
            'status' => CartStatus::ACTIVE,
            'expires_at' => now()->addDays(7),
        ]);
    }
}
