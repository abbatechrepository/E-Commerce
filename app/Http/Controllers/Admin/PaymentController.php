<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use Illuminate\Contracts\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('admin.payments.index', [
            'payments' => Payment::query()
                ->with(['order', 'transactions'])
                ->latest()
                ->paginate(12),
            'webhookLogs' => PaymentWebhookLog::query()
                ->with('payment.order')
                ->latest()
                ->take(12)
                ->get(),
        ]);
    }

    public function show(Payment $payment): View
    {
        return view('admin.payments.show', [
            'payment' => $payment->load(['order.customer.user', 'transactions', 'webhookLogs']),
        ]);
    }
}
