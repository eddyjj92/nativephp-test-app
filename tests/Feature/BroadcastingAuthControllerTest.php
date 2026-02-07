<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BroadcastingAuthControllerTest extends TestCase
{
    public function test_broadcasting_auth_returns_401_without_token(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
        ]);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123456.789',
            'channel_name' => 'presence-online.users',
        ]);

        $response->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_broadcasting_auth_proxies_to_external_backend(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
            '*/broadcasting/auth' => Http::response([
                'auth' => 'mrdq0t1beqxxhppyhgch:abc123signature',
                'channel_data' => '{"user_id":1,"user_info":{"id":1,"name":"Test"}}',
            ], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'test-token',
        ])->postJson('/broadcasting/auth', [
            'socket_id' => '123456.789',
            'channel_name' => 'presence-online.users',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['auth', 'channel_data']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/broadcasting/auth') &&
                $request->hasHeader('Authorization', 'Bearer test-token') &&
                $request['socket_id'] === '123456.789' &&
                $request['channel_name'] === 'presence-online.users';
        });
    }

    public function test_broadcasting_auth_forwards_error_from_backend(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
            '*/broadcasting/auth' => Http::response(['message' => 'Forbidden'], 403),
        ]);

        $response = $this->withSession([
            'compay_token' => 'expired-token',
        ])->postJson('/broadcasting/auth', [
            'socket_id' => '123456.789',
            'channel_name' => 'presence-online.users',
        ]);

        $response->assertForbidden()
            ->assertJson(['message' => 'Forbidden']);
    }

    public function test_broadcasting_auth_returns_422_when_missing_params(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'test-token',
        ])->postJson('/broadcasting/auth', []);

        $response->assertUnprocessable()
            ->assertJson(['message' => 'Missing socket_id or channel_name.']);
    }

    public function test_broadcasting_auth_parses_raw_form_body_as_fallback(): void
    {
        Http::fake([
            '*/currencies' => Http::response(['currencies' => []], 200),
            '*/settings' => Http::response(['settings' => $this->fakeSettings()], 200),
            '*/broadcasting/auth' => Http::response([
                'auth' => 'mrdq0t1beqxxhppyhgch:abc123signature',
                'channel_data' => '{"user_id":1,"user_info":{"id":1,"name":"Test"}}',
            ], 200),
        ]);

        // Simulate what NativePHP does: send raw form-encoded body
        // without proper Content-Type being parsed by Laravel
        $response = $this->withSession([
            'compay_token' => 'test-token',
        ])->call(
            'POST',
            '/broadcasting/auth',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded'],
            'socket_id=123456.789&channel_name=presence-online.users'
        );

        $response->assertOk()
            ->assertJsonStructure(['auth', 'channel_data']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/broadcasting/auth') &&
                $request['socket_id'] === '123456.789' &&
                $request['channel_name'] === 'presence-online.users';
        });
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
