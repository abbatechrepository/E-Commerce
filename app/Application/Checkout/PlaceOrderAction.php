<?php

namespace App\Application\Checkout;

use App\Application\Audit\AuditLogger;
use App\Enums\CartStatus;
use App\Enums\InventoryMovementType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Events\OrderPlaced;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use App\Services\Payments\FakePaymentGatewayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlaceOrderAction
{
    public function __construct(
        private readonly FakePaymentGatewayService $paymentGatewayService,
        private readonly AuditLogger $auditLogger,
    ) {
    }

    public function execute(Cart $cart, Address $address, string $paymentMethod, string $shippingService, ?Coupon $coupon = null, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($cart, $address, $paymentMethod, $shippingService, $coupon, $notes): Order {
            $cart->loadMissing('customer.user', 'items.product.artist', 'items.product.inventory');

            $subtotal = 0.0;

            foreach ($cart->items as $item) {
                /** @var Product $product */
                $product = $item->product;

                if (! $product || ! $product->isPurchasable() || ($product->inventory?->available_quantity ?? 0) < $item->quantity) {
                    abort(422, 'O carrinho possui itens indisponiveis.');
                }

                $subtotal += (float) $item->subtotal;
            }

            $discountTotal = $coupon ? min((float) $coupon->discount_value, $subtotal) : 0.0;
            $shippingTotal = $subtotal >= 350 ? 0.0 : 24.90;
            $total = max($subtotal - $discountTotal + $shippingTotal, 0);

            $order = Order::query()->create([
                'customer_id' => $cart->customer_id,
                'address_id' => $address->id,
                'coupon_id' => $coupon?->id,
                'order_number' => 'ECM-'.strtoupper(Str::random(10)),
                'status' => OrderStatus::PENDING_PAYMENT,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'shipping_total' => $shippingTotal,
                'total' => $total,
                'currency' => 'BRL',
                'notes' => $notes,
                'shipping_recipient_name' => $address->recipient_name,
                'shipping_zip_code' => $address->zip_code,
                'shipping_street' => $address->street,
                'shipping_number' => $address->number,
                'shipping_complement' => $address->complement,
                'shipping_district' => $address->district,
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,
                'shipping_country' => $address->country,
                'shipping_reference' => $address->reference,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;

                $order->items()->create([
                    'product_id' => $product?->id,
                    'product_name_snapshot' => $product?->name,
                    'artist_name_snapshot' => $product?->artist?->name,
                    'album_title_snapshot' => $product?->album_title,
                    'sku_snapshot' => $product?->sku,
                    'media_format_snapshot' => $product?->media_format,
                    'disc_condition_snapshot' => $product?->disc_condition,
                    'sleeve_condition_snapshot' => $product?->sleeve_condition,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);

                $product->inventory()->decrement('available_quantity', $item->quantity);
                $product->inventory()->increment('reserved_quantity', $item->quantity);

                InventoryMovement::query()->create([
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'user_id' => $cart->customer?->user_id,
                    'type' => InventoryMovementType::RESERVATION,
                    'quantity' => $item->quantity,
                    'reason' => 'Estoque reservado durante o checkout',
                    'metadata' => ['order_number' => $order->order_number],
                ]);
            }

            $payment = Payment::query()->create([
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'provider' => 'portfolio_fake_gateway',
                'status' => PaymentStatus::PENDING,
                'amount' => $order->total,
                'idempotency_key' => (string) Str::uuid(),
            ]);

            Shipment::query()->create([
                'order_id' => $order->id,
                'carrier' => 'Entrega Portifolio',
                'service_name' => $shippingService,
                'status' => ShipmentStatus::PENDING,
                'shipping_cost' => $shippingTotal,
                'estimated_delivery_date' => now()->addDays(7)->toDateString(),
            ]);

            $order->statusHistory()->create([
                'from_status' => null,
                'to_status' => OrderStatus::PENDING_PAYMENT,
                'changed_by' => $cart->customer?->user_id,
                'reason' => 'Pedido criado via checkout',
                'metadata' => ['cart_id' => $cart->id],
            ]);

            $gatewayResponse = $this->paymentGatewayService->createTransaction($payment, [
                'payment_id' => $payment->id,
                'order_number' => $order->order_number,
                'amount' => $order->total,
                'simulate_status' => 'pending',
                'callback_url' => route('api.gateway.webhooks.payment-status'),
            ]);

            $cart->update([
                'status' => CartStatus::CONVERTED,
                'converted_at' => now(),
            ]);

            $this->auditLogger->log('order.placed', $order, null, $order->toArray(), [
                'payment_transaction' => $gatewayResponse['transaction_code'] ?? null,
            ]);

            OrderPlaced::dispatch($order);

            return $order->refresh()->load(['items', 'payment.transactions', 'shipment']);
        });
    }
}
