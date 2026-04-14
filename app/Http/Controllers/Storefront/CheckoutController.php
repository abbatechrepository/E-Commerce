<?php

namespace App\Http\Controllers\Storefront;

use App\Application\Cart\GetOrCreateCartAction;
use App\Application\Checkout\PlaceOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CheckoutRequest;
use App\Models\Coupon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function create(GetOrCreateCartAction $getOrCreateCartAction): View
    {
        $customer = auth()->user()->customer()->with('addresses')->firstOrFail();
        $cart = $getOrCreateCartAction->execute($customer, session()->getId());
        $cart->load('items.product.artist', 'items.product.inventory');

        return view('storefront.checkout.create', [
            'customer' => $customer,
            'cart' => $cart,
            'subtotal' => $cart->items->sum('subtotal'),
        ]);
    }

    public function store(CheckoutRequest $request, PlaceOrderAction $placeOrderAction, GetOrCreateCartAction $getOrCreateCartAction): RedirectResponse
    {
        $customer = $request->user()->customer;
        $cart = $getOrCreateCartAction->execute($customer, $request->session()->getId());
        $address = $customer->addresses()->findOrFail($request->integer('address_id'));
        $coupon = $request->filled('coupon_code')
            ? Coupon::query()->where('code', $request->string('coupon_code'))->first()
            : null;

        $order = $placeOrderAction->execute(
            $cart->load('items.product.artist', 'items.product.inventory', 'customer'),
            $address,
            $request->string('payment_method')->toString(),
            $request->string('shipping_service')->toString(),
            $coupon,
            $request->string('notes')->toString() ?: null,
        );

        return redirect()->route('customer.orders.show', $order)->with('status', 'Pedido criado com sucesso. Pagamento em processamento.');
    }
}
