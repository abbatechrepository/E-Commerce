<?php

use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Payments\FakeGatewayController;
use App\Http\Controllers\Payments\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('api.products.show');

    Route::middleware('auth')->group(function (): void {
        Route::get('/me', [ProfileController::class, 'show'])->name('api.profile.show');
        Route::post('/checkout', CheckoutController::class)->name('api.checkout.store');
        Route::get('/orders', [OrderController::class, 'index'])->name('api.orders.index');
        Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('api.orders.show');
    });

    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('api.admin.dashboard');
        Route::apiResource('products', AdminProductController::class)
            ->parameters(['products' => 'product:slug'])
            ->names('api.admin.products');
        Route::post('/products/{product:slug}/publish', [AdminProductController::class, 'publish'])->name('api.admin.products.publish');
    });

    Route::prefix('gateway/fake-payments')->group(function (): void {
        Route::post('/transactions', [FakeGatewayController::class, 'store'])->name('api.gateway.transactions.store');
        Route::get('/transactions/{transactionCode}', [FakeGatewayController::class, 'show'])->name('api.gateway.transactions.show');
        Route::post('/transactions/{transactionCode}/refund', [FakeGatewayController::class, 'refund'])->name('api.gateway.transactions.refund');
        Route::post('/transactions/{transactionCode}/cancel', [FakeGatewayController::class, 'cancel'])->name('api.gateway.transactions.cancel');
        Route::post('/transactions/{transactionCode}/simulate-status', [FakeGatewayController::class, 'simulateStatus'])->name('api.gateway.transactions.simulate-status');
        Route::post('/webhooks/payment-status', PaymentWebhookController::class)->name('api.gateway.webhooks.payment-status');
    });
});
