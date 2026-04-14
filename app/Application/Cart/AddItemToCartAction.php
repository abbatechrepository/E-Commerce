<?php

namespace App\Application\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use DomainException;

class AddItemToCartAction
{
    public function execute(Cart $cart, Product $product, int $quantity = 1): Cart
    {
        if (! $product->isPurchasable()) {
            throw new DomainException('Este produto nao esta disponivel para compra.');
        }

        $cartItem = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $newQuantity = ($cartItem->exists ? $cartItem->quantity : 0) + $quantity;

        if (($product->inventory?->available_quantity ?? 0) < $newQuantity) {
            throw new DomainException('A quantidade solicitada excede o estoque disponivel.');
        }

        $unitPrice = (float) $product->effective_price;

        $cartItem->fill([
            'quantity' => $newQuantity,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice * $newQuantity,
        ])->save();

        return $cart->refresh();
    }
}
