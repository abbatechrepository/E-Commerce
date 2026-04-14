<?php

namespace App\Http\Controllers\Api;

use App\Application\Checkout\PlaceOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;

class CheckoutController extends Controller
{
    public function __invoke(CheckoutRequest $request, PlaceOrderAction $placeOrderAction): OrderResource
    {
        $cart = Cart::query()->with(['items.product.artist', 'customer'])->findOrFail($request->integer('cart_id'));
        $address = Address::query()->where('customer_id', $request->user()->customer->id)->findOrFail($request->integer('address_id'));
        $coupon = $request->filled('coupon_code')
            ? Coupon::query()->where('code', $request->string('coupon_code'))->first()
            : null;

        $order = $placeOrderAction->execute(
            $cart,
            $address,
            $request->string('payment_method')->toString(),
            $request->string('shipping_service')->toString(),
            $coupon,
            $request->string('notes')->toString() ?: null,
        );

        return new OrderResource($order);
    }
}
