<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::query()
            ->with(['items', 'payment', 'shipment'])
            ->where('customer_id', auth()->user()->customer->id)
            ->latest()
            ->paginate();

        return OrderResource::collection($orders);
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order->load(['items', 'payment.transactions', 'shipment']));
    }
}
