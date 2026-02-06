<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CartTransportationCostTest extends TestCase
{
    public function test_transportation_cost_endpoint_returns_compay_response_shape(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
            '*/transportation_costs/get_price_for_weight*' => Http::response([
                'price' => '4.80',
                'price_with_discount' => '4.20',
                'weight_range' => '1-2kg',
                'has_discount' => true,
            ], 200),
        ]);

        $response = $this->withSession(['compay_token' => 'test-token'])
            ->getJson('/cart/transportation-cost?cost_ring_id=3&weight_kg=1.75&total_cost=50');

        $response->assertOk()
            ->assertJson([
                'price' => '4.80',
                'price_with_discount' => '4.20',
                'weight_range' => '1-2kg',
                'has_discount' => true,
            ]);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return str_contains($request->url(), '/transportation_costs/get_price_for_weight') &&
                (int) $data['cost_ring_id'] === 3 &&
                (float) $data['weight_kg'] === 1.75 &&
                (float) $data['total_cost'] === 50.0 &&
                $request->hasHeader('Authorization', 'Bearer test-token');
        });
    }

    public function test_transportation_cost_endpoint_validates_required_fields(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
        ]);

        $response = $this->getJson('/cart/transportation-cost');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cost_ring_id', 'weight_kg']);
    }

    /**
     * @return array<string, mixed>
     */
    private function fakeSettings(): array
    {
        return [
            'site_name' => 'Compay',
            'site_active' => true,
            'email' => 'test@example.com',
            'phone' => null,
            'address' => 'Address',
            'facebook' => null,
            'x' => null,
            'instagram' => null,
            'logo_light' => 'logo-light.png',
            'logo_dark' => 'logo-dark.png',
            'terms_conditions' => 'Terms',
            'privacy_policies' => 'Privacy',
            'cookies_policies' => 'Cookies',
            'legal_notice' => 'Legal',
            'frequently_questions' => [],
        ];
    }
}
