<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        protected CompayMarketService $compayMarketService
    ) {}

    public function __invoke(): Response
    {
        $province = session('selected_province');
        $currency = session('selected_currency');

        $page = request()->integer('page', 1);
        $categoryParams = ['page' => $page];

        if ($province) {
            $categoryParams['province_id'] = $province->id;
        }

        if ($currency) {
            $categoryParams['currency'] = $currency->isoCode;
        }

        $categoriesResponse = $this->compayMarketService->getCategories($categoryParams, cache: true);
        $categories = $categoriesResponse['categories'] ?? [];

        // Build local next page URL instead of using the external API URL
        $nextPageUrl = null;
        if (! empty($categories['next_page_url'])) {
            $nextPage = ($categories['current_page'] ?? $page) + 1;
            $nextPageUrl = route('home', ['page' => $nextPage]);
        }

        return Inertia::render('Home', [
            // Banners are deferred - loaded after initial render
            'banners' => Inertia::defer(fn () => $this->compayMarketService->getBanners('active', cache: true)),

            // Categories load immediately (they're smaller)
            'categories' => Inertia::merge($categories['data'] ?? []),
            'categoriesNextPageUrl' => $nextPageUrl,

            // Products are deferred - loaded after initial render
            'recommendedProducts' => Inertia::defer(function () use ($province, $currency) {
                if (! $province || ! $currency) {
                    return [];
                }
                $marketplaceHome = $this->compayMarketService->getMarketplaceHome(
                    provinceSlug: $province->slug,
                    currency: $currency->isoCode,
                    cache: true
                );

                return $marketplaceHome?->recommendedProducts ?? [];
            }),

            'newArrivals' => Inertia::defer(function () use ($province, $currency) {
                if (! $province || ! $currency) {
                    return [];
                }
                $marketplaceHome = $this->compayMarketService->getMarketplaceHome(
                    provinceSlug: $province->slug,
                    currency: $currency->isoCode,
                    cache: true
                );

                return $marketplaceHome?->newArrivals ?? [];
            }),
        ]);
    }
}
