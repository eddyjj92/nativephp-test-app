<?php

use App\Http\Controllers\BroadcastingAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductsController;
use App\Services\CompayMarketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductsController::class, 'search'])->name('products.search');
Route::get('/products/{categoryId}', [ProductsController::class, 'index'])->name('products.category');

Route::group(['prefix' => 'cart'], function () {
    Route::get('/', function () {
        return Inertia::render('Cart');
    })->name('cart');
    Route::post('/', [CartController::class, 'store'])->name('cart.add');
    Route::put('/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/{productId}', [CartController::class, 'destroy'])->name('cart.remove');
    Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/transportation-cost', [CartController::class, 'transportationCost'])->name('cart.transportation-cost');
});

Route::get('/checkout', function (Request $request, CompayMarketService $service) {
    $token = $request->session()->get('compay_token');

    return Inertia::render('Checkout', [
        'beneficiaries' => Inertia::defer(function () use ($token, $service) {
            if (! $token) {
                return [];
            }

            try {
                $response = $service->setToken($token)->getBeneficiaries([
                    'order_by' => 'desc',
                    'page' => 1,
                    'per_page' => 100,
                ]);

                return $response['beneficiaries']['data'] ?? [];
            } catch (\Throwable $e) {
                return [];
            }
        }),
    ]);
})->name('checkout');

Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations');
Route::get('/conversations/{id}', [ConversationController::class, 'show'])->name('chat.show');
Route::get('/conversations/{id}/messages', [ConversationController::class, 'messages'])->name('chat.messages');
Route::post('/conversations/{id}/messages', [ConversationController::class, 'sendMessage'])->name('chat.sendMessage');
Route::patch('/conversations/{id}/read', [ConversationController::class, 'markAsRead'])->name('chat.markAsRead');

Route::get('/product/{id}', [ProductsController::class, 'show'])->name('products.show');
Route::post('/product/{id}/refresh', [ProductsController::class, 'refresh'])->name('products.refresh');

Route::group(['prefix' => 'favorites'], function () {
    Route::get('/', function () {
        return Inertia::render('Favorites');
    })->name('favorites');
    Route::post('/', [FavoritesController::class, 'store'])->name('favorites.add');
    Route::delete('/{productId}', [FavoritesController::class, 'destroy'])->name('favorites.remove');
    Route::delete('/', [FavoritesController::class, 'clear'])->name('favorites.clear');
});

Route::get('/profile', function () {
    return Inertia::render('Profile');
})->name('profile');

Route::get('/profile/edit', function () {
    return Inertia::render('ProfileEdit');
})->name('profile.edit.mobile');

Route::post('/profile/info/update', [\App\Http\Controllers\CompayAuthController::class, 'updateProfile'])->name('profile.info.update');

Route::get('/orders', [\App\Http\Controllers\CompayAuthController::class, 'orders'])->name('orders');
Route::get('/beneficiaries', [\App\Http\Controllers\CompayAuthController::class, 'beneficiaries'])->name('beneficiaries');
Route::get('/beneficiaries/create', [\App\Http\Controllers\CompayAuthController::class, 'beneficiaryCreate'])->name('beneficiaries.create');
Route::post('/beneficiaries', [\App\Http\Controllers\CompayAuthController::class, 'beneficiaryStore'])->name('beneficiaries.store');
Route::get('/beneficiaries/{id}/edit', [\App\Http\Controllers\CompayAuthController::class, 'beneficiaryEdit'])->name('beneficiaries.edit');
Route::put('/beneficiaries/{id}', [\App\Http\Controllers\CompayAuthController::class, 'beneficiaryUpdate'])->name('beneficiaries.update');
Route::delete('/beneficiaries/{id}', [\App\Http\Controllers\CompayAuthController::class, 'beneficiaryDestroy'])->name('beneficiaries.destroy');

Route::group(['prefix' => 'help'], function () {
    Route::get('/', function () {
        return Inertia::render('help/Index');
    })->name('help');
    Route::get('/faq', function () {
        return Inertia::render('help/Faq');
    })->name('help.faq');
    Route::get('/contact', function () {
        return Inertia::render('help/Contact');
    })->name('help.contact');
    Route::get('/terms', function () {
        return Inertia::render('help/Terms');
    })->name('help.terms');
    Route::get('/privacy', function () {
        return Inertia::render('help/Privacy');
    })->name('help.privacy');
    Route::get('/cookies', function () {
        return Inertia::render('help/Cookies');
    })->name('help.cookies');
    Route::get('/legal', function () {
        return Inertia::render('help/Legal');
    })->name('help.legal');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['prefix' => 'locations'], function () {
    Route::get('/', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/', [LocationController::class, 'store'])->name('locations.set');
});

Route::post('/currency', [CurrencyController::class, 'store'])->name('currency.set');

Route::post('/broadcasting/auth', BroadcastingAuthController::class)->name('broadcasting.auth');

Route::post('/login', [\App\Http\Controllers\CompayAuthController::class, 'login'])->name('login.store');
Route::post('/logout', [\App\Http\Controllers\CompayAuthController::class, 'logout'])->name('logout');
Route::post('/auth/google/start', [\App\Http\Controllers\CompayAuthController::class, 'startGoogleAuth'])->name('auth.google.start');
Route::post('/auth/google/consume', [\App\Http\Controllers\CompayAuthController::class, 'consumeGoogleAuth'])->name('auth.google.consume');
Route::get('/auth/google/callback', [\App\Http\Controllers\CompayAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/profile/update', [\App\Http\Controllers\CompayAuthController::class, 'updateProfile'])->name('profile.update.api');
Route::post('/orders/checkout', [OrderController::class, 'store'])->name('orders.checkout');

Route::get('/native/preview', function () {
    $path = request('path');

    if (! $path || ! \Illuminate\Support\Facades\File::exists($path)) {
        return response('error', 404);
    }

    try {
        $fileContent = \Illuminate\Support\Facades\File::get($path);
        $type = \Illuminate\Support\Facades\File::mimeType($path);
        $base64 = base64_encode($fileContent);

        return response("data:$type;base64,$base64");
    } catch (\Exception $e) {
        return response('error', 500);
    }
})->name('native.preview');

require __DIR__.'/settings.php';
