<?php

// use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('shop.products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('shop.products.show');
Route::get('/categories/{category:slug}', [ProductController::class, 'byCategory'])->name('shop.categories.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/thankyou/{order}', [CheckoutController::class, 'thankYou'])->name('checkout.thankyou');
    // Route::get('/profile', function () {
    //     return Inertia::render('Shop/Profile');
    // })->name('profile');
    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
