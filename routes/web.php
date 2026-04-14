<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AuditController as AdminAuditController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('storefront.home');
Route::get('/products', [ProductController::class, 'index'])->name('storefront.products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('storefront.products.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::prefix('customer')->group(function (): void {
        Route::get('/dashboard', CustomerDashboardController::class)->name('customer.dashboard');
        Route::get('/addresses', [AddressController::class, 'index'])->name('customer.addresses.index');
        Route::post('/addresses', [AddressController::class, 'store'])->name('customer.addresses.store');
        Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('customer.addresses.update');
        Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
        Route::get('/orders/{order:order_number}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    });
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product:slug}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product:slug}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::post('/products/{product:slug}/publish', [AdminProductController::class, 'publish'])->name('admin.products.publish');
    Route::post('/products/{product:slug}/images', [AdminProductController::class, 'uploadImage'])->name('admin.products.images.store');
    Route::post('/products/{product:slug}/images/{image}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('admin.products.images.primary');
    Route::delete('/products/{product:slug}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('admin.products.images.destroy');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order:order_number}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('admin.payments.show');
    Route::get('/audit', [AdminAuditController::class, 'index'])->name('admin.audit.index');
});
