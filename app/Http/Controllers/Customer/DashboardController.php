<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $customer = auth()->user()->customer()->with(['addresses', 'metric'])->firstOrFail();
        $recentOrders = Order::query()
            ->where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', [
            'customer' => $customer,
            'recentOrders' => $recentOrders,
        ]);
    }
}
