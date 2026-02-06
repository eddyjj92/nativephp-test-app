<?php

namespace Tests\Feature;

use App\DTOs\CurrencyDTO;
use App\DTOs\MarketplaceHomeDTO;
use App\DTOs\ProductDTO;
use App\Services\CompayMarketService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CompayMarketServiceTest extends TestCase
{
    public function test_get_currencies_returns_array_of_currency_dtos(): void
    {
        Http::fake([
            '*/currencies' => Http::response([
                'currencies' => [
                    [
                        'id' => 1,
                        'name' => 'Dólar Estadounidense',
                        'iso_code' => 'USD',
                        'is_default' => true,
                        'conversion_value' => 1,
                    ],
                    [
                        'id' => 2,
                        'name' => 'Euro',
                        'iso_code' => 'EUR',
                        'is_default' => false,
                        'conversion_value' => 0.84459,
                    ],
                ],
            ], 200),
        ]);

        $service = new CompayMarketService;
        $currencies = $service->getCurrencies();

        $this->assertCount(2, $currencies);
        $this->assertInstanceOf(CurrencyDTO::class, $currencies[0]);
        $this->assertInstanceOf(CurrencyDTO::class, $currencies[1]);

        $this->assertEquals(1, $currencies[0]->id);
        $this->assertEquals('USD', $currencies[0]->isoCode);
        $this->assertTrue($currencies[0]->isDefault);

        $this->assertEquals(2, $currencies[1]->id);
        $this->assertEquals('EUR', $currencies[1]->isoCode);
        $this->assertFalse($currencies[1]->isDefault);
    }

    public function test_get_marketplace_home_returns_dto_with_recommended_products_and_new_arrivals(): void
    {
        Http::fake([
            '*/products/marketplace_home*' => Http::response([
                'recommended_products' => [
                    [
                        'id' => 127,
                        'name' => 'FILETE DE PESCADO DE MAR 1KG',
                        'slug' => 'filete-de-pescado-de-mar-1kg',
                        'code' => '00151',
                        'description' => 'Un filete de pescado',
                        'type' => null,
                        'sale_price' => 9.98,
                        'weight' => 1,
                        'free_shipping' => false,
                        'image' => 'https://example.com/image.png',
                        'status' => 'ENABLED',
                        'recommended' => true,
                        'stock' => 49,
                        'category' => null,
                        'active_discounts' => [],
                    ],
                ],
                'new_arrivals' => [
                    [
                        'id' => 200,
                        'name' => 'PRODUCTO NUEVO',
                        'slug' => 'producto-nuevo',
                        'code' => '00200',
                        'description' => 'Nuevo producto',
                        'type' => null,
                        'sale_price' => 15.00,
                        'weight' => 1,
                        'free_shipping' => false,
                        'image' => 'https://example.com/new.png',
                        'status' => 'ENABLED',
                        'recommended' => false,
                        'stock' => 10,
                        'category' => null,
                        'active_discounts' => [],
                    ],
                ],
            ], 200),
        ]);

        $service = new CompayMarketService;
        $result = $service->getMarketplaceHome('la-habana', 'USD');

        $this->assertInstanceOf(MarketplaceHomeDTO::class, $result);

        // Recommended products
        $this->assertCount(1, $result->recommendedProducts);
        $this->assertInstanceOf(ProductDTO::class, $result->recommendedProducts[0]);
        $this->assertEquals(127, $result->recommendedProducts[0]->id);

        // New arrivals
        $this->assertCount(1, $result->newArrivals);
        $this->assertInstanceOf(ProductDTO::class, $result->newArrivals[0]);
        $this->assertEquals(200, $result->newArrivals[0]->id);
        $this->assertEquals('PRODUCTO NUEVO', $result->newArrivals[0]->name);
    }

    public function test_get_marketplace_home_returns_empty_arrays_when_no_data(): void
    {
        Http::fake([
            '*/products/marketplace_home*' => Http::response([
                'recommended_products' => [],
                'new_arrivals' => [],
            ], 200),
        ]);

        $service = new CompayMarketService;
        $result = $service->getMarketplaceHome('la-habana', 'USD');

        $this->assertInstanceOf(MarketplaceHomeDTO::class, $result);
        $this->assertEmpty($result->recommendedProducts);
        $this->assertEmpty($result->newArrivals);
    }

    public function test_get_product_passes_currency_parameter(): void
    {
        Http::fake([
            '*/products/127?currency=EUR' => Http::response([
                'product' => [
                    'id' => 127,
                    'name' => 'FILETE DE PESCADO DE MAR 1KG',
                    'slug' => 'filete-de-pescado-de-mar-1kg',
                    'code' => '00151',
                    'description' => 'Un filete de pescado',
                    'type' => null,
                    'sale_price' => 8.50,
                    'weight' => 1,
                    'free_shipping' => false,
                    'image' => 'https://example.com/image.png',
                    'status' => 'ENABLED',
                    'recommended' => true,
                    'stock' => 49,
                    'category' => null,
                    'active_discounts' => [],
                ],
            ], 200),
        ]);

        $service = new CompayMarketService;
        $product = $service->getProduct('127', 'EUR');

        $this->assertInstanceOf(ProductDTO::class, $product);
        $this->assertEquals(127, $product->id);
        $this->assertEquals(8.50, $product->salePrice);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/products/127') &&
                   $request->data()['currency'] === 'EUR';
        });
    }

    public function test_get_products_passes_currency_parameter(): void
    {
        Http::fake([
            '*/products?province_id=1&currency=USD' => Http::response([
                'products' => [
                    'data' => [
                        [
                            'id' => 127,
                            'name' => 'FILETE DE PESCADO DE MAR 1KG',
                            'slug' => 'filete-de-pescado-de-mar-1kg',
                            'code' => '00151',
                            'description' => 'Un filete de pescado',
                            'type' => null,
                            'sale_price' => 9.98,
                            'weight' => 1,
                            'free_shipping' => false,
                            'image' => 'https://example.com/image.png',
                            'status' => 'ENABLED',
                            'recommended' => true,
                            'stock' => 49,
                            'category' => null,
                            'active_discounts' => [],
                        ],
                    ],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 15,
                    'total' => 1,
                ],
            ], 200),
        ]);

        $service = new CompayMarketService;
        $result = $service->getProducts(['province_id' => 1, 'currency' => 'USD']);

        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(ProductDTO::class, $result['data'][0]);
        $this->assertEquals(9.98, $result['data'][0]->salePrice);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/products') &&
                   $request->data()['currency'] === 'USD' &&
                   $request->data()['province_id'] == '1';
        });
    }

    public function test_get_transportation_price_for_weight_passes_required_parameters(): void
    {
        Http::fake([
            '*/transportation_costs/get_price_for_weight*' => Http::response([
                'price' => '3.50',
                'price_with_discount' => '3.00',
                'weight_range' => '1-3kg',
                'has_discount' => true,
            ], 200),
        ]);

        $service = new CompayMarketService;
        $response = $service->getTransportationPriceForWeight(2, 2.5, 120.30);

        $this->assertSame('3.50', $response['price']);
        $this->assertSame('3.00', $response['price_with_discount']);
        $this->assertSame('1-3kg', $response['weight_range']);
        $this->assertTrue($response['has_discount']);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return str_contains($request->url(), '/transportation_costs/get_price_for_weight') &&
                (int) $data['cost_ring_id'] === 2 &&
                (float) $data['weight_kg'] === 2.5 &&
                (float) $data['total_cost'] === 120.3;
        });
    }
}
