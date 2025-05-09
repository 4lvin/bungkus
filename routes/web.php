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

// Cart routes - available to all users, including guests
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/update/{id}', [CartController::class, 'updateCartItem'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'removeCartItem'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');

// Routes that require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/thankyou/{order}', [CheckoutController::class, 'thankYou'])->name('checkout.thankyou');
    // Route::get('/profile', function () {
    //     return Inertia::render('Shop/Profile');
    // })->name('profile');
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Merge cart after login
    Route::post('/cart/merge', [CartController::class, 'mergeCart'])->name('cart.merge');
});
// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
