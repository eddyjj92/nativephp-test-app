<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/products/{category?}', function (?string $category = null) {
    return Inertia::render('Products', [
        'category' => $category ? ucfirst($category) : 'Electronics',
    ]);
})->name('products');

Route::get('/cart', function () {
    return Inertia::render('Cart');
})->name('cart');

Route::get('/checkout', function () {
    return Inertia::render('Checkout');
})->name('checkout');

Route::get('/conversations', function () {
    return Inertia::render('Conversations');
})->name('conversations');

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

require __DIR__.'/settings.php';
