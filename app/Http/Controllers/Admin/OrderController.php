<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with(['customer.user', 'payment', 'shipment'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load([
                'customer.user',
                'items',
                'payment.transactions',
                'shipment',
                'statusHistory.changedBy',
            ]),
        ]);
    }
}
