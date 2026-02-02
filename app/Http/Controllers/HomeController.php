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

        return Inertia::render('Home', [
            'banners' => $banners,
        ]);
    }
}
