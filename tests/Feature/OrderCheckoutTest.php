<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderCheckoutTest extends TestCase
{
    public function test_checkout_order_creates_order_and_returns_redirect_url(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
            '*/orders' => Http::response([
                'message' => 'Orden creada exitosamente',
                'order' => [
                    'id' => 2002,
                    'redirect_url' => 'https://sandbox-checkout.revolut.com/payment-link/test',
                ],
            ], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'test-token',
            'cart' => [
                1 => ['id' => 1, 'quantity' => 2],
                2 => ['id' => 2, 'quantity' => 1],
            ],
        ])->postJson('/orders/checkout', [
            'currency' => 'USD',
            'beneficiary_id' => 881,
            'delivery_type_id' => 1,
            'notes' => 'test',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Orden creada exitosamente',
                'redirect_url' => 'https://sandbox-checkout.revolut.com/payment-link/test',
            ]);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return str_contains($request->url(), '/orders') &&
                $request->hasHeader('Authorization', 'Bearer test-token') &&
                $data['currency'] === 'USD' &&
                (int) $data['beneficiary_id'] === 881 &&
                (int) $data['delivery_type_id'] === 1 &&
                count($data['cart']) === 2;
        });
    }

    public function test_checkout_order_validates_payload(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'test-token',
            'cart' => [1 => ['id' => 1, 'quantity' => 1]],
        ])->postJson('/orders/checkout', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['currency', 'beneficiary_id', 'delivery_type_id']);
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
