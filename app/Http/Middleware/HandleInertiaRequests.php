<?php

namespace App\Http\Middleware;

use App\Services\CompayMarketService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(protected CompayMarketService $compayMarketService) {}

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $currencies = $this->compayMarketService->getCurrencies(cache: true);
        $selectedCurrency = $request->session()->get('selected_currency');

        if (! $selectedCurrency && count($currencies) > 0) {
            $selectedCurrency = collect($currencies)->first(fn ($c) => $c->isDefault) ?? $currencies[0];
            $request->session()->put('selected_currency', $selectedCurrency);
        }

        $province = $request->session()->get('selected_province');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'settings' => $this->compayMarketService->getSettings(cache: true),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => $request->user() ?? $request->session()->get('compay_user'),
            ],
            'location' => [
                'province' => $request->session()->get('selected_province'),
                'municipality' => $request->session()->get('selected_municipality'),
            ],
            'currencies' => $currencies,
            'selectedCurrency' => $selectedCurrency,
            'cart' => Inertia::defer(function () use ($request, $selectedCurrency, $province) {
                $cartSession = $request->session()->get('cart', []);
                $cartItems = [];

                foreach ($cartSession as $id => $item) {
                    $product = $this->compayMarketService->getProduct(
                        id: (string) $id,
                        currency: $selectedCurrency?->isoCode,
                        provinceSlug: $province?->slug,
                        cache: true
                    );

                    if ($product) {
                        $cartItems[] = [
                            'product' => $product,
                            'quantity' => $item['quantity'],
                            'price' => $product->getDiscountedPrice(),
                        ];
                    }
                }

                return [
                    'items' => $cartItems,
                    'count' => collect($cartItems)->sum('quantity'),
                    'total' => collect($cartItems)->sum(fn ($item) => $item['price'] * $item['quantity']),
                ];
            }),
            'favorites' => Inertia::defer(function () use ($request, $selectedCurrency, $province) {
                $favoritesSession = $request->session()->get('favorites', []);
                $favoriteItems = [];

                foreach ($favoritesSession as $id => $item) {
                    $product = $this->compayMarketService->getProduct(
                        id: (string) $id,
                        currency: $selectedCurrency?->isoCode,
                        provinceSlug: $province?->slug,
                        cache: true
                    );

                    if ($product) {
                        $favoriteItems[] = [
                            'product' => $product,
                        ];
                    }
                }

                return [
                    'items' => $favoriteItems,
                    'count' => count($favoriteItems),
                    'ids' => array_keys($favoritesSession),
                ];
            }),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
