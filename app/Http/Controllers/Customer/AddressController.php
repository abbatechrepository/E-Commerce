<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        $customer = auth()->user()->customer()->with('addresses')->firstOrFail();

        return view('customer.addresses.index', [
            'addresses' => $customer->addresses,
        ]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $customer = $request->user()->customer;

        if ($request->boolean('is_default')) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $customer->addresses()->create($request->validated());

        return back()->with('status', 'Endereço salvo com sucesso.');
    }

    public function update(StoreAddressRequest $request, Address $address): RedirectResponse
    {
        abort_unless($address->customer_id === $request->user()->customer->id, 403);

        if ($request->boolean('is_default')) {
            $request->user()->customer->addresses()->update(['is_default' => false]);
        }

        $address->update($request->validated());

        return back()->with('status', 'Endereço atualizado com sucesso.');
    }
}
