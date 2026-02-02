<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{categorySlug}', [ProductsController::class, 'index'])->name('products.category');

Route::get('/cart', function () {
    return Inertia::render('Cart');
})->name('cart');

Route::get('/checkout', function () {
    return Inertia::render('Checkout');
})->name('checkout');

Route::get('/conversations', function () {
    return Inertia::render('Conversations');
})->name('conversations');

Route::get('/conversations/{id}', function (string $id) {
    return Inertia::render('Chat', ['id' => $id]);
})->name('chat.show');

Route::get('/product/{id}', function (int $id) {
    return Inertia::render('ProductDetail', [
        'productId' => $id,
    ]);
})->name('product.detail');

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

require __DIR__.'/settings.php';
