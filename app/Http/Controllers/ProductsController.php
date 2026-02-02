<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Inertia\Inertia;
use Inertia\Response;

class ProductsController extends Controller
{
    public function __construct(
        protected CompayMarketService $compayMarketService
    ) {}

    public function index(?string $categorySlug = null): Response
    {
        $province = request()->session()->get('selected_province');
        $provinceId = $province?->id;

        $page = request()->integer('page', 1);

        $params = [
            'page' => $page,
            'province_id' => $provinceId,
        ];

        if ($categorySlug) {
            $params['category'] = $categorySlug;
        }

        $productsResponse = $this->compayMarketService->getProducts($params, cache: true);

        // Build local next page URL
        $nextPageUrl = null;
        if (! empty($productsResponse['next_page_url'])) {
            $nextPage = $productsResponse['current_page'] + 1;
            $routeParams = ['page' => $nextPage];
            $nextPageUrl = $categorySlug
                ? route('products.category', ['categorySlug' => $categorySlug, ...$routeParams])
                : route('products.index', $routeParams);
        }

        return Inertia::render('Products/Index', [
            'products' => Inertia::merge($productsResponse['data']),
            'productsNextPageUrl' => $nextPageUrl,
            'currentPage' => $productsResponse['current_page'],
            'lastPage' => $productsResponse['last_page'],
            'total' => $productsResponse['total'],
            'categorySlug' => $categorySlug,
        ]);
    }
}
