<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'customers' => Customer::count(),
            'orders' => Order::count(),
            'revenue' => Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->sum('total'),
            'pending_orders' => Order::where('status', 'pending_payment')->count(),
        ]);
    }
}
