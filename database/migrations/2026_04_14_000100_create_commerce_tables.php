<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('marketing_consent')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('label', 60)->nullable();
            $table->string('recipient_name', 120);
            $table->string('zip_code', 20)->index();
            $table->string('street', 160);
            $table->string('number', 20);
            $table->string('complement', 120)->nullable();
            $table->string('district', 120);
            $table->string('city', 120);
            $table->string('state', 80);
            $table->string('country', 80)->default('Brazil');
            $table->string('reference', 160)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['customer_id', 'is_default']);
        });

        Schema::create('artists', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 160)->index();
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->string('country', 80)->nullable();
            $table->timestamps();
        });

        Schema::create('genres', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->index();
            $table->string('slug', 140)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->index();
            $table->string('slug', 140)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('sku', 60)->unique();
            $table->string('name', 180)->index();
            $table->string('slug', 200)->unique();
            $table->foreignId('artist_id')->constrained();
            $table->foreignId('genre_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('album_title', 180);
            $table->longText('description');
            $table->year('release_year')->nullable();
            $table->string('label_name', 160)->nullable();
            $table->string('country', 80)->nullable();
            $table->string('media_format', 40)->index();
            $table->string('disc_condition', 40)->index();
            $table->string('sleeve_condition', 40)->index();
            $table->string('rarity_level', 40)->index();
            $table->decimal('price', 10, 2);
            $table->decimal('promotional_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('weight', 8, 3)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->string('status', 40)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_rare')->default(false)->index();
            $table->boolean('is_new_arrival')->default(false)->index();
            $table->boolean('is_on_sale')->default(false)->index();
            $table->boolean('is_best_seller')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_active', 'published_at']);
            $table->index(['genre_id', 'category_id', 'is_active', 'status'], 'products_catalog_lookup_idx');
        });

        Schema::create('product_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_text', 180)->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'position']);
            $table->index(['product_id', 'is_primary']);
        });

        Schema::create('inventories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('available_quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('minimum_quantity')->default(0);
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 60)->unique();
            $table->string('description')->nullable();
            $table->string('discount_type', 30)->index();
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_order_value', 10, 2)->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        Schema::create('carts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 120)->nullable()->index();
            $table->string('status', 40)->index();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
        });

        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->unique(['cart_id', 'product_id']);
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number', 40)->unique();
            $table->string('status', 40)->index();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('shipping_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->char('currency', 3)->default('BRL');
            $table->text('notes')->nullable();
            $table->string('shipping_recipient_name', 120);
            $table->string('shipping_zip_code', 20);
            $table->string('shipping_street', 160);
            $table->string('shipping_number', 20);
            $table->string('shipping_complement', 120)->nullable();
            $table->string('shipping_district', 120);
            $table->string('shipping_city', 120);
            $table->string('shipping_state', 80);
            $table->string('shipping_country', 80);
            $table->string('shipping_reference', 160)->nullable();
            $table->timestamp('placed_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name_snapshot', 180);
            $table->string('artist_name_snapshot', 160);
            $table->string('album_title_snapshot', 180)->nullable();
            $table->string('sku_snapshot', 60);
            $table->string('media_format_snapshot', 40)->nullable();
            $table->string('disc_condition_snapshot', 40)->nullable();
            $table->string('sleeve_condition_snapshot', 40)->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40)->index();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('method', 40)->index();
            $table->string('provider', 60)->index();
            $table->string('status', 40)->index();
            $table->decimal('amount', 10, 2);
            $table->string('external_reference', 120)->nullable()->index();
            $table->string('idempotency_key', 120)->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_code', 120)->unique();
            $table->string('provider', 60)->index();
            $table->string('status', 40)->index();
            $table->decimal('amount', 10, 2);
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('provider_response_code', 40)->nullable();
            $table->string('provider_message', 255)->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'created_at']);
        });

        Schema::create('payment_webhook_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 80)->index();
            $table->string('external_transaction_code', 120)->nullable()->index();
            $table->json('payload');
            $table->json('headers')->nullable();
            $table->boolean('processed')->default(false)->index();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedInteger('processing_attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['processed', 'created_at']);
        });

        Schema::create('shipments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('carrier', 80);
            $table->string('service_name', 80);
            $table->string('tracking_code', 120)->nullable()->unique();
            $table->string('status', 40)->index();
            $table->decimal('shipping_cost', 10, 2);
            $table->date('estimated_delivery_date')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 40)->index();
            $table->unsignedInteger('quantity');
            $table->string('reason', 180)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'type', 'created_at']);
        });

        Schema::create('favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['customer_id', 'product_id']);
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('entity_type', 120)->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->string('action', 80)->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['action', 'created_at']);
        });

        Schema::create('coupon_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code_snapshot', 60);
            $table->decimal('discount_amount', 10, 2);
            $table->timestamp('redeemed_at');
            $table->timestamps();

            $table->index(['coupon_id', 'customer_id']);
        });

        Schema::create('customer_metrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->decimal('average_ticket', 12, 2)->default(0);
            $table->timestamp('last_order_at')->nullable()->index();
            $table->boolean('is_recurring')->default(false)->index();
            $table->foreignId('favorite_genre_id')->nullable()->constrained('genres')->nullOnDelete();
            $table->foreignId('favorite_artist_id')->nullable()->constrained('artists')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_metrics');
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('payment_webhook_logs');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('artists');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('customers');
    }
};
