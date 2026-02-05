<?php

namespace Tests\Feature\Products;

use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductsIndexDeferredTest extends TestCase
{
    public function test_products_listing_is_deferred_and_can_be_loaded(): void
    {
        Http::fake([
            '*/currencies' => Http::response([
                'currencies' => [
                    [
                        'id' => 1,
                        'name' => 'Dólar',
                        'iso_code' => 'USD',
                        'is_default' => true,
                        'conversion_value' => 1,
                    ],
                ],
            ], 200),
            '*/settings' => Http::response([
                'settings' => [
                    'site_name' => 'Compay Market',
                    'site_active' => true,
                    'email' => 'test@example.com',
                    'phone' => null,
                    'address' => 'La Habana',
                    'facebook' => null,
                    'x' => null,
                    'instagram' => null,
                    'logo_light' => 'https://example.com/logo-light.png',
                    'logo_dark' => 'https://example.com/logo-dark.png',
                    'terms_conditions' => 'terms',
                    'privacy_policies' => 'privacy',
                    'cookies_policies' => 'cookies',
                    'legal_notice' => 'legal',
                    'frequently_questions' => [],
                ],
            ], 200),
            '*/products*' => Http::response([
                'products' => [
                    'data' => [
                        [
                            'id' => 127,
                            'name' => 'FILETE DE PESCADO',
                            'slug' => 'filete-de-pescado',
                            'code' => '00127',
                            'description' => 'Descripción',
                            'type' => null,
                            'sale_price' => 9.98,
                            'weight' => 1,
                            'free_shipping' => false,
                            'image' => 'https://example.com/image.png',
                            'status' => 'ENABLED',
                            'recommended' => true,
                            'stock' => 10,
                            'category' => null,
                            'active_discounts' => [],
                        ],
                    ],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 15,
                    'total' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                ],
            ], 200),
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Products/Index')
            ->where('categoryId', null)
            ->missing('listing')
            ->loadDeferredProps(fn (Assert $deferredPage) => $deferredPage
                ->has('listing.products', 1)
                ->where('listing.currentPage', 1)
                ->where('listing.lastPage', 1)
                ->where('listing.total', 1)
                ->where('listing.products.0.id', 127)
            )
        );

        Http::assertSent(fn ($request) => str_contains($request->url(), '/products'));
    }
}
