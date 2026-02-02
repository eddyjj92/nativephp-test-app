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

        $page = request()->integer('page', 1);
        $categoriesResponse = $this->compayMarketService->getCategories(['page' => $page], cache: true);
        $categories = $categoriesResponse['categories'] ?? [];

        return Inertia::render('Home', [
            'banners' => $banners,
            'categories' => Inertia::merge($categories['data'] ?? []),
            'categoriesNextPageUrl' => $categories['next_page_url'] ?? null,
        ]);
    }
}
