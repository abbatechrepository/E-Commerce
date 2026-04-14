<?php

namespace App\Application\Cart;

use App\Models\CartItem;
use DomainException;

class UpdateCartItemAction
{
    public function execute(CartItem $cartItem, int $quantity): CartItem
    {
        if ($quantity < 1) {
            throw new DomainException('A quantidade precisa ser no minimo 1.');
        }

        $availableQuantity = $cartItem->product?->inventory?->available_quantity ?? 0;

        if ($availableQuantity < $quantity) {
            throw new DomainException('A quantidade solicitada excede o estoque disponivel.');
        }

        $cartItem->update([
            'quantity' => $quantity,
            'subtotal' => (float) $cartItem->unit_price * $quantity,
        ]);

        return $cartItem->refresh();
    }
}
