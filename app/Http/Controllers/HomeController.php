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
        $banners = $this->compayMarketService->getBanners('active', cache: true);

        // Get marketplace home data with recommended products
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

        // Get marketplace home data with recommended products
        $province = session('selected_province');
        $currency = session('selected_currency');

        $marketplaceHome = null;
        if ($province && $currency) {
            $marketplaceHome = $this->compayMarketService->getMarketplaceHome(
                provinceSlug: $province->slug,
                currency: $currency->isoCode,
                cache: true
            );
        }

        return Inertia::render('Home', [
            'banners' => $banners,
            'categories' => Inertia::merge($categories['data'] ?? []),
            'categoriesNextPageUrl' => $nextPageUrl,
            'recommendedProducts' => $marketplaceHome?->recommendedProducts ?? [],
            'newArrivals' => $marketplaceHome?->newArrivals ?? [],
        ]);
    }
}
