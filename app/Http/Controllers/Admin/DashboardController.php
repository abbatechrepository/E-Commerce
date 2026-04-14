<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'metrics' => [
                'products' => Product::count(),
                'customers' => Customer::count(),
                'orders' => Order::count(),
                'revenue' => Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->sum('total'),
                'pending_payments' => Payment::where('status', 'pending')->count(),
            ],
            'recentOrders' => Order::query()->latest()->take(5)->get(),
            'recentAudits' => AuditLog::query()->with('user')->latest()->take(6)->get(),
            'recentWebhooks' => PaymentWebhookLog::query()->with('payment.order')->latest()->take(6)->get(),
            'adminUsers' => User::query()
                ->whereIn('role', ['admin', 'manager'])
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }
}
