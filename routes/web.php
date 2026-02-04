<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{categoryId}', [ProductsController::class, 'index'])->name('products.category');

Route::group(['prefix' => 'cart'], function () {
    Route::get('/', function () {
        return Inertia::render('Cart');
    })->name('cart');
    Route::post('/', [CartController::class, 'store'])->name('cart.add');
    Route::put('/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/{productId}', [CartController::class, 'destroy'])->name('cart.remove');
    Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');
});

Route::get('/checkout', function () {
    return Inertia::render('Checkout');
})->name('checkout');

Route::get('/conversations', function () {
    return Inertia::render('Conversations');
})->name('conversations');

Route::get('/conversations/{id}', function (string $id) {
    return Inertia::render('Chat', ['id' => $id]);
})->name('chat.show');

Route::get('/product/{id}', [ProductsController::class, 'show'])->name('products.show');
Route::post('/product/{id}/refresh', [ProductsController::class, 'refresh'])->name('products.refresh');

Route::get('/favorites', function () {
    return Inertia::render('Favorites');
})->name('favorites');

Route::get('/profile', function () {
    return Inertia::render('Profile');
})->name('profile');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['prefix' => 'locations'], function () {
    Route::get('/', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/', [LocationController::class, 'store'])->name('locations.set');
});

Route::post('/currency', [CurrencyController::class, 'store'])->name('currency.set');

Route::post('/login', [\App\Http\Controllers\CompayAuthController::class, 'login'])->name('login.api');
Route::post('/logout', [\App\Http\Controllers\CompayAuthController::class, 'logout'])->name('logout.api');
Route::post('/profile/update', [\App\Http\Controllers\CompayAuthController::class, 'updateProfile'])->name('profile.update');

require __DIR__.'/settings.php';
