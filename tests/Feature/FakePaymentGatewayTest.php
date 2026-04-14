<?php

namespace Tests\Feature;

use App\Enums\CartStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Artist;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Genre;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_pending_transaction_and_gateway_can_approve_and_refund_it(): void
    {
        Queue::fake();

        $customer = Customer::factory()->create();
        $address = Address::factory()->create([
            'customer_id' => $customer->id,
            'is_default' => true,
        ]);

        $product = Product::factory()->create([
            'artist_id' => Artist::factory(),
            'genre_id' => Genre::factory(),
            'category_id' => Category::factory(),
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'image_path' => 'products/demo/test.jpg',
            'alt_text' => 'Test cover',
            'position' => 1,
            'is_primary' => true,
        ]);

        $product->inventory()->create([
            'available_quantity' => 3,
            'reserved_quantity' => 0,
            'minimum_quantity' => 1,
        ]);

        $cart = Cart::query()->create([
            'customer_id' => $customer->id,
            'status' => CartStatus::ACTIVE,
            'expires_at' => now()->addDay(),
        ]);

        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->effective_price,
            'subtotal' => $product->effective_price,
        ]);

        $this->actingAs($customer->user)
            ->postJson('/api/v1/checkout', [
                'cart_id' => $cart->id,
                'address_id' => $address->id,
                'payment_method' => 'credit_card',
                'shipping_service' => 'Standard',
            ])
            ->assertCreated();

        $order = $customer->orders()->with('payment.transactions', 'items.product.inventory')->latest()->first();

        $this->assertNotNull($order);
        $this->assertSame(OrderStatus::PENDING_PAYMENT, $order->status);
        $this->assertSame(PaymentStatus::PENDING, $order->payment->status);

        $transactionCode = $order->payment->transactions->first()->transaction_code;

        $this->postJson("/api/v1/gateway/fake-payments/transactions/{$transactionCode}/simulate-status", [
            'status' => PaymentStatus::APPROVED->value,
        ])->assertOk();

        $order->refresh();
        $order->load('payment');

        $this->assertSame(OrderStatus::PAID, $order->status);
        $this->assertSame(PaymentStatus::APPROVED, $order->payment->status);

        $this->postJson("/api/v1/gateway/fake-payments/transactions/{$transactionCode}/refund", [
            'reason' => 'Collector cancellation during demo flow',
        ])->assertOk();

        $order->refresh();
        $order->load('payment', 'items.product.inventory');

        $this->assertSame(OrderStatus::REFUNDED, $order->status);
        $this->assertSame(PaymentStatus::REFUNDED, $order->payment->status);
        $this->assertSame(3, $order->items->first()->product->inventory->available_quantity);
        $this->assertSame(0, $order->items->first()->product->inventory->reserved_quantity);
    }
}
