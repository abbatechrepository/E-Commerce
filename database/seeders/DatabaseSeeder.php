<?php

namespace Database\Seeders;

use App\Enums\CartStatus;
use App\Enums\CouponDiscountType;
use App\Enums\InventoryMovementType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Models\Address;
use App\Models\Artist;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Genre;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use App\Support\DemoCatalogCoverGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function __construct(private readonly DemoCatalogCoverGenerator $demoCatalogCoverGenerator)
    {
    }

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@ecommerce.test'],
            [
                'name' => 'Portfolio Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'last_login_at' => now()->subDays(7),
            ]
        );

        $demoCustomerUser = User::query()->updateOrCreate(
            ['email' => 'customer@ecommerce.test'],
            [
                'name' => 'Demo Customer',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
                'last_login_at' => now()->subDays(3),
            ]
        );

        Customer::query()->updateOrCreate(
            ['user_id' => $demoCustomerUser->id],
            [
                'phone' => '(11) 99999-0000',
                'birth_date' => '1992-06-15',
                'marketing_consent' => true,
                'notes' => 'Portfolio demo customer',
            ]
        );

        $genres = collect([
            ['name' => 'Jazz', 'slug' => 'jazz'],
            ['name' => 'Soul', 'slug' => 'soul'],
            ['name' => 'MPB', 'slug' => 'mpb'],
            ['name' => 'Rock', 'slug' => 'rock'],
            ['name' => 'Disco', 'slug' => 'disco'],
        ])->map(fn (array $genre) => Genre::query()->firstOrCreate($genre, ['description' => fake()->sentence()]));

        $categories = collect([
            ['name' => 'LP', 'slug' => 'lp'],
            ['name' => 'Compacto', 'slug' => 'compacto'],
            ['name' => 'Importado', 'slug' => 'importado'],
            ['name' => 'Colecionador', 'slug' => 'colecionador'],
        ])->map(fn (array $category) => Category::query()->firstOrCreate($category, ['description' => fake()->sentence()]));

        $artists = collect([
            ['name' => 'Milton Nascimento', 'slug' => 'milton-nascimento', 'country' => 'Brazil'],
            ['name' => 'Gal Costa', 'slug' => 'gal-costa', 'country' => 'Brazil'],
            ['name' => 'The Velvet Echoes', 'slug' => 'the-velvet-echoes', 'country' => 'United Kingdom'],
            ['name' => 'Blue Saturn Ensemble', 'slug' => 'blue-saturn-ensemble', 'country' => 'United States'],
            ['name' => 'Orquestra Aurora', 'slug' => 'orquestra-aurora', 'country' => 'Brazil'],
        ])->map(fn (array $artist) => Artist::query()->firstOrCreate(
            ['slug' => $artist['slug']],
            ['name' => $artist['name'], 'description' => fake()->paragraph(), 'country' => $artist['country']]
        ));

        Coupon::factory()->count(4)->create();

        Coupon::query()->firstOrCreate([
            'code' => 'RARITY10',
        ], [
            'description' => '10% off on curated classics',
            'discount_type' => CouponDiscountType::PERCENTAGE,
            'discount_value' => 10,
            'minimum_order_value' => 150,
            'usage_limit' => 100,
            'usage_count' => 12,
            'starts_at' => now()->subDays(15),
            'ends_at' => now()->addDays(45),
            'is_active' => true,
        ]);

        $products = Product::factory()
            ->count(30)
            ->make()
            ->each(function (Product $product) use ($artists, $genres, $categories): void {
                $product->artist()->associate($artists->random());
                $product->genre()->associate($genres->random());
                $product->category()->associate($categories->random());
                $product->save();

                $inventoryQuantity = $product->is_rare ? 1 : fake()->numberBetween(2, 12);

                $product->inventory()->create([
                    'available_quantity' => $inventoryQuantity,
                    'reserved_quantity' => 0,
                    'minimum_quantity' => 1,
                ]);

                $this->demoCatalogCoverGenerator->ensure($product, true);
            });

        Product::query()
            ->with(['artist', 'genre', 'images'])
            ->get()
            ->each(fn (Product $product) => $this->demoCatalogCoverGenerator->ensure($product));

        $customers = Customer::factory()
            ->count(12)
            ->create()
            ->each(function (Customer $customer): void {
                Address::factory()->count(rand(1, 3))->create([
                    'customer_id' => $customer->id,
                    'is_default' => false,
                ]);

                $customer->addresses()->first()?->update(['is_default' => true]);
            });

        $customers->take(6)->each(function (Customer $customer) use ($products): void {
            $address = $customer->addresses()->first();
            $status = collect([
                OrderStatus::PAID,
                OrderStatus::PROCESSING,
                OrderStatus::SHIPPED,
                OrderStatus::DELIVERED,
                OrderStatus::PENDING_PAYMENT,
            ])->random();

            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'address_id' => $address?->id,
                'order_number' => 'ECM-'.Str::upper(Str::random(10)),
                'status' => $status,
                'subtotal' => 0,
                'discount_total' => 0,
                'shipping_total' => 24.90,
                'total' => 0,
                'currency' => 'BRL',
                'shipping_recipient_name' => $address?->recipient_name ?? $customer->user->name,
                'shipping_zip_code' => $address?->zip_code ?? '01000-000',
                'shipping_street' => $address?->street ?? 'Rua Demo',
                'shipping_number' => $address?->number ?? '100',
                'shipping_complement' => $address?->complement,
                'shipping_district' => $address?->district ?? 'Centro',
                'shipping_city' => $address?->city ?? 'Sao Paulo',
                'shipping_state' => $address?->state ?? 'SP',
                'shipping_country' => $address?->country ?? 'Brazil',
                'shipping_reference' => $address?->reference,
                'placed_at' => now()->subDays(rand(5, 90)),
            ]);

            $subtotal = 0;

            foreach ($products->random(rand(1, 3)) as $product) {
                $quantity = $product->is_rare ? 1 : rand(1, 2);
                $lineTotal = (float) $product->effective_price * $quantity;
                $subtotal += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name_snapshot' => $product->name,
                    'artist_name_snapshot' => $product->artist->name,
                    'album_title_snapshot' => $product->album_title,
                    'sku_snapshot' => $product->sku,
                    'media_format_snapshot' => $product->media_format,
                    'disc_condition_snapshot' => $product->disc_condition,
                    'sleeve_condition_snapshot' => $product->sleeve_condition,
                    'unit_price' => $product->effective_price,
                    'quantity' => $quantity,
                    'subtotal' => $lineTotal,
                ]);

                InventoryMovement::query()->create([
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'user_id' => $customer->user_id,
                    'type' => InventoryMovementType::SALE,
                    'quantity' => $quantity,
                    'reason' => 'Seeded demo order',
                    'metadata' => ['order_number' => $order->order_number],
                ]);
            }

            $discount = rand(0, 1) ? 15.00 : 0;

            $order->update([
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'total' => $subtotal - $discount + $order->shipping_total,
                'paid_at' => in_array($status, [OrderStatus::PAID, OrderStatus::PROCESSING, OrderStatus::SHIPPED, OrderStatus::DELIVERED], true) ? now()->subDays(rand(2, 60)) : null,
            ]);

            $order->statusHistory()->create([
                'from_status' => null,
                'to_status' => $status,
                'changed_by' => $customer->user_id,
                'reason' => 'Seeded order lifecycle',
            ]);

            $paymentStatus = $status === OrderStatus::PENDING_PAYMENT
                ? PaymentStatus::PENDING
                : PaymentStatus::APPROVED;

            $payment = Payment::query()->create([
                'order_id' => $order->id,
                'method' => 'credit_card',
                'provider' => 'portfolio_fake_gateway',
                'status' => $paymentStatus,
                'amount' => $order->total,
                'external_reference' => 'FAKE-REF-'.Str::upper(Str::random(8)),
                'idempotency_key' => (string) Str::uuid(),
                'paid_at' => $paymentStatus === PaymentStatus::APPROVED ? $order->paid_at : null,
            ]);

            $payment->transactions()->create([
                'transaction_code' => 'TXN-'.Str::upper(Str::random(12)),
                'provider' => 'portfolio_fake_gateway',
                'status' => $paymentStatus,
                'amount' => $payment->amount,
                'request_payload' => ['seeded' => true],
                'response_payload' => ['status' => $paymentStatus->value],
                'provider_response_code' => '200',
                'provider_message' => 'Seeded transaction',
                'requested_at' => $order->placed_at,
                'responded_at' => $order->placed_at,
            ]);

            Shipment::query()->create([
                'order_id' => $order->id,
                'carrier' => 'Portfolio Express',
                'service_name' => 'Standard',
                'tracking_code' => $status !== OrderStatus::PENDING_PAYMENT ? 'BR'.rand(100000, 999999).'ECM' : null,
                'status' => match ($status) {
                    OrderStatus::DELIVERED => ShipmentStatus::DELIVERED,
                    OrderStatus::SHIPPED => ShipmentStatus::SHIPPED,
                    OrderStatus::PROCESSING, OrderStatus::PAID => ShipmentStatus::READY_FOR_DISPATCH,
                    default => ShipmentStatus::PENDING,
                },
                'shipping_cost' => $order->shipping_total,
                'estimated_delivery_date' => now()->addDays(rand(3, 10))->toDateString(),
                'shipped_at' => in_array($status, [OrderStatus::SHIPPED, OrderStatus::DELIVERED], true) ? now()->subDays(rand(1, 10)) : null,
                'delivered_at' => $status === OrderStatus::DELIVERED ? now()->subDays(rand(0, 4)) : null,
            ]);
        });

        Cart::query()->create([
            'customer_id' => $customers->first()->id,
            'status' => CartStatus::ACTIVE,
            'expires_at' => now()->addDays(5),
        ]);
    }
}
