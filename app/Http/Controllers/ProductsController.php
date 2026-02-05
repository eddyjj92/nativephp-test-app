<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductsController extends Controller
{
    public function __construct(
        protected CompayMarketService $compayMarketService
    ) {}

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json(['products' => []]);
        }

        $province = session('selected_province');
        $currency = session('selected_currency');

        $params = [
            'search' => $query,
            'per_page' => 10,
            'province_id' => $province?->id,
        ];

        if ($currency) {
            $params['currency'] = $currency->isoCode;
        }

        $productsResponse = $this->compayMarketService->getProducts($params, cache: true);

        return response()->json([
            'products' => $productsResponse['data'],
        ]);
    }

    public function index(Request $request): Response
    {
        $province = session('selected_province');
        $currency = session('selected_currency');
        $provinceId = $province?->id;

        $page = request()->integer('page', 1);

        $params = [
            'page' => $page,
            'province_id' => $provinceId,
        ];

        if ($currency) {
            $params['currency'] = $currency->isoCode;
        }

        $params['category_id'] = null;
        if ($request->query('category_id')) {
            $params['category_id'] = $request->query('category_id');
        }

        if ($request->query('search')) {
            $params['search'] = $request->query('search');
        }

        $productsResponse = $this->compayMarketService->getProducts($params, cache: true);

        // Build local next page URL
        $nextPageUrl = null;
        if (! empty($productsResponse['next_page_url'])) {
            $nextPage = $productsResponse['current_page'] + 1;
            $routeParams = ['page' => $nextPage];
            $nextPageUrl = $params['category_id']
                ? route('products.category', ['categoryId' => $params['category_id'], ...$routeParams])
                : route('products.index', $routeParams);
        }

        return Inertia::render('Products/Index', [
            'products' => Inertia::merge($productsResponse['data']),
            'productsNextPageUrl' => $nextPageUrl,
            'currentPage' => $productsResponse['current_page'],
            'lastPage' => $productsResponse['last_page'],
            'total' => $productsResponse['total'],
            'categoryId' => $params['category_id'],
        ]);
    }

    public function show(int $id): Response
    {
        $currency = session('selected_currency');
        $province = session('selected_province');

        $product = $this->compayMarketService->getProduct(
            id: (string) $id,
            currency: $currency?->isoCode,
            provinceSlug: $province?->slug,
            cache: true
        );

        if (! $product) {
            abort(404, 'Producto no encontrado');
        }

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Invalida el caché de un producto y lo vuelve a obtener de la API.
     * Útil cuando las imágenes temporales expiran (Error 403).
     */
    public function refresh(int $id): JsonResponse
    {
        $currency = session('selected_currency');
        $province = session('selected_province');

        $params = [];
        if ($currency) {
            $params['currency'] = $currency->isoCode;
        }
        if ($province) {
            $params['province_slug'] = $province->slug;
        }

        // 1. Obtener datos frescos de la API directamente (sin usar el caché)
        $product = $this->compayMarketService->getProduct(
            id: (string) $id,
            currency: $currency?->isoCode,
            provinceSlug: $province?->slug,
            cache: false // Bypass cache
        );

        if ($product) {
            // 2. Si obtuvimos el producto, invalidamos el caché anterior
            $this->compayMarketService->clearCache("/products/{$id}", $params);

            // 3. Volvemos a pedirlo con cache: true para que se guarde la versión fresca
            $this->compayMarketService->getProduct(
                id: (string) $id,
                currency: $currency?->isoCode,
                provinceSlug: $province?->slug,
                cache: true
            );
        }

        return response()->json([
            'success' => (bool) $product,
            'product' => $product,
        ]);
    }
}
