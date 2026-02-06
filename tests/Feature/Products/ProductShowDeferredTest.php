<?php

namespace Tests\Feature\Products;

use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductShowDeferredTest extends TestCase
{
    public function test_product_show_is_deferred_and_can_be_loaded(): void
    {
        $this->fakeSharedRequests();

        $response = $this->get(route('products.show', ['id' => 127]));

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Products/Show')
            ->missing('product')
            ->loadDeferredProps(fn (Assert $deferredPage) => $deferredPage
                ->where('product.id', 127)
                ->where('product.name', 'FILETE DE PESCADO')
                ->where('product.salePrice', 9.98)
            )
        );

        Http::assertSent(fn ($request) => str_contains($request->url(), '/products/127'));
    }

    protected function fakeSharedRequests(): void
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
            '*/products/127*' => Http::response([
                'product' => [
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
            ], 200),
        ]);
    }
}
