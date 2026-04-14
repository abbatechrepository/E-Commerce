<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['items', 'payment', 'shipment'])
            ->where('customer_id', auth()->user()->customer->id)
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('customer.orders.show', [
            'order' => $order->load(['items', 'payment.transactions', 'shipment', 'statusHistory']),
        ]);
    }
}
