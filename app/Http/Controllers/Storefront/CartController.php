<?php

namespace App\Http\Controllers\Storefront;

use App\Application\Cart\AddItemToCartAction;
use App\Application\Cart\GetOrCreateCartAction;
use App\Application\Cart\RemoveCartItemAction;
use App\Application\Cart\UpdateCartItemAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function index(GetOrCreateCartAction $getOrCreateCartAction): View
    {
        $cart = $getOrCreateCartAction->execute(auth()->user()?->customer, session()->getId());
        $cart->load('items.product.artist', 'items.product.inventory');

        return view('storefront.cart.index', [
            'cart' => $cart,
            'subtotal' => $cart->items->sum('subtotal'),
        ]);
    }

    public function store(AddToCartRequest $request, GetOrCreateCartAction $getOrCreateCartAction, AddItemToCartAction $addItemToCartAction): RedirectResponse
    {
        $cart = $getOrCreateCartAction->execute($request->user()?->customer, $request->session()->getId());
        $product = Product::query()->with('inventory')->findOrFail($request->integer('product_id'));

        try {
            $addItemToCartAction->execute($cart, $product, $request->integer('quantity', 1));
        } catch (DomainException $exception) {
            return back()->withErrors(['cart' => $exception->getMessage()]);
        }

        return redirect()->route('cart.index')->with('status', 'Item adicionado ao carrinho.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem, UpdateCartItemAction $updateCartItemAction): RedirectResponse
    {
        $this->ensureCartOwnership($cartItem);

        try {
            $updateCartItemAction->execute($cartItem->load('product.inventory'), $request->integer('quantity'));
        } catch (DomainException $exception) {
            return back()->withErrors(['cart' => $exception->getMessage()]);
        }

        return back()->with('status', 'Carrinho atualizado.');
    }

    public function destroy(CartItem $cartItem, RemoveCartItemAction $removeCartItemAction): RedirectResponse
    {
        $this->ensureCartOwnership($cartItem);
        $removeCartItemAction->execute($cartItem);

        return back()->with('status', 'Item removido do carrinho.');
    }

    private function ensureCartOwnership(CartItem $cartItem): void
    {
        $cartItem->loadMissing('cart');
        $userCustomerId = auth()->user()?->customer?->id;
        $sessionId = session()->getId();

        abort_unless(
            $cartItem->cart?->customer_id === $userCustomerId || $cartItem->cart?->session_id === $sessionId,
            403
        );
    }
}
