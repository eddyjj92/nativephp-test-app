<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * @var array{data: array<int, mixed>, next_page_url: string|null}|null
     */
    protected ?array $categoriesPayload = null;

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

        return Inertia::render('Home', [
            'connection_exception' => false,

            // Banners are deferred - loaded after initial render
            'banners' => Inertia::defer(fn () => $this->compayMarketService->getBanners('active', cache: true)),

            // Categories are deferred and remain mergeable for infinite scroll.
            'categories' => Inertia::defer(
                fn () => $this->getCategoriesPayload($categoryParams, $page)['data']
            )->merge(),
            'categoriesNextPageUrl' => Inertia::defer(
                fn () => $this->getCategoriesPayload($categoryParams, $page)['next_page_url']
            ),

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

    /**
     * @param  array<string, mixed>  $categoryParams
     * @return array{data: array<int, mixed>, next_page_url: string|null}
     */
    protected function getCategoriesPayload(array $categoryParams, int $page): array
    {
        if ($this->categoriesPayload !== null) {
            return $this->categoriesPayload;
        }

        try {
            $categoriesResponse = $this->compayMarketService->getCategories($categoryParams, cache: true);
            $categories = $categoriesResponse['categories'] ?? [];
        } catch (ConnectionException $e) {
            Log::warning('Failed to fetch categories from API.', [
                'message' => $e->getMessage(),
            ]);

            $this->categoriesPayload = [
                'data' => [],
                'next_page_url' => null,
            ];

            return $this->categoriesPayload;
        }

        $nextPageUrl = null;
        if (! empty($categories['next_page_url'])) {
            $nextPage = ($categories['current_page'] ?? $page) + 1;
            $nextPageUrl = route('home', ['page' => $nextPage]);
        }

        $this->categoriesPayload = [
            'data' => $categories['data'] ?? [],
            'next_page_url' => $nextPageUrl,
        ];

        return $this->categoriesPayload;
    }
}
