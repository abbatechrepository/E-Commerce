<?php

namespace App\Application\Cart;

use App\Models\CartItem;

class RemoveCartItemAction
{
    public function execute(CartItem $cartItem): void
    {
        $cartItem->delete();
    }
}
