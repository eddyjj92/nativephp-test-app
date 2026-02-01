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

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
