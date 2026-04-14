<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Events\PaymentStatusUpdated;
use App\Models\CustomerMetric;

class UpdateCustomerMetrics
{
    public function handle(OrderPlaced|PaymentStatusUpdated $event): void
    {
        $order = $event instanceof OrderPlaced ? $event->order : $event->payment->order;

        if (! $order?->customer_id) {
            return;
        }

        $paidOrders = $order->customer->orders()
            ->whereIn('status', ['paid', 'processing', 'shipped', 'delivered', 'refunded'])
            ->get();

        CustomerMetric::query()->updateOrCreate(
            ['customer_id' => $order->customer_id],
            [
                'total_orders' => $paidOrders->count(),
                'total_spent' => $paidOrders->sum('total'),
                'average_ticket' => round((float) $paidOrders->avg('total'), 2),
                'last_order_at' => $paidOrders->max('placed_at'),
                'is_recurring' => $paidOrders->count() > 1,
            ],
        );
    }
}
