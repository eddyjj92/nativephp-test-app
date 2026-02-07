<?php

namespace Tests\Feature;

use App\DTOs\UserDTO;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    private function fakeUserPayload(): array
    {
        return [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_country_code' => '+53',
            'phone' => '55555555',
            'address' => 'Test Address',
            'status' => 'enabled',
            'email_verified_at' => now()->toIso8601String(),
            'avatar' => null,
            'google_id' => '123456789',
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
            'avatar_backup' => null,
            'admin' => null,
            'customer' => [
                'id' => 1,
                'privacy_policies_accepted' => true,
                'user_id' => 1,
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ],
            'permissions' => [],
        ];
    }

    public function test_start_google_auth_returns_state_and_auth_url(): void
    {
        Http::fake([
            '*/auth/google/mobile/start' => Http::response([
                'state' => 'fake-uuid-state',
                'auth_url' => 'https://accounts.google.com/o/oauth2/auth?client_id=test',
                'expires_in' => 300,
            ], 200),
        ]);

        $response = $this->postJson('/auth/google/start');

        $response->assertOk()
            ->assertJsonStructure(['state', 'auth_url'])
            ->assertJson([
                'state' => 'fake-uuid-state',
            ]);

        $this->assertStringContainsString('accounts.google.com', $response->json('auth_url'));
    }

    public function test_start_google_auth_returns_500_when_external_api_fails(): void
    {
        Http::fake([
            '*/auth/google/mobile/start' => Http::response([], 500),
        ]);

        $response = $this->postJson('/auth/google/start');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'No se pudo iniciar la autenticación con Google.',
            ]);
    }

    public function test_consume_google_auth_stores_session_and_returns_user(): void
    {
        $userPayload = $this->fakeUserPayload();

        Http::fake([
            '*/auth/google/mobile/consume' => Http::response([
                'token' => 'fake-bearer-token-123',
                'user' => $userPayload,
            ], 200),
        ]);

        $response = $this->postJson('/auth/google/consume', [
            'state' => 'fake-uuid-state',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'user' => [
                    'id' => 1,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ]);

        $this->assertEquals('fake-bearer-token-123', session('compay_token'));
        $this->assertInstanceOf(UserDTO::class, session('compay_user'));
        $this->assertEquals('test@example.com', session('compay_user')->email);
    }

    public function test_consume_google_auth_rejects_non_customer_user(): void
    {
        $userPayload = $this->fakeUserPayload();
        $userPayload['customer'] = null;

        Http::fake([
            '*/auth/google/mobile/consume' => Http::response([
                'token' => 'fake-bearer-token-123',
                'user' => $userPayload,
            ], 200),
        ]);

        $response = $this->postJson('/auth/google/consume', [
            'state' => 'fake-uuid-state',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Esta cuenta no tiene acceso a la aplicación. Solo los clientes pueden iniciar sesión.',
            ]);

        $this->assertNull(session('compay_token'));
    }

    public function test_consume_google_auth_fails_when_pending(): void
    {
        Http::fake([
            '*/auth/google/mobile/consume' => Http::response([
                'error' => 'La autenticación aún no está lista para consumo.',
                'status' => 'pending',
            ], 422),
        ]);

        $response = $this->postJson('/auth/google/consume', [
            'state' => 'fake-uuid-state',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'La autenticación aún no está lista para consumo.',
                'status' => 'pending',
            ]);
        $this->assertNull(session('compay_token'));
    }

    public function test_consume_google_auth_fails_when_state_expired(): void
    {
        Http::fake([
            '*/auth/google/mobile/consume' => Http::response([
                'error' => 'OAuth state inválido o expirado.',
            ], 404),
        ]);

        $response = $this->postJson('/auth/google/consume', [
            'state' => 'expired-state',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'OAuth state inválido o expirado.',
            ]);
        $this->assertNull(session('compay_token'));
    }

    public function test_consume_google_auth_requires_state_parameter(): void
    {
        $response = $this->postJson('/auth/google/consume', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['state']);
    }

    public function test_google_callback_deep_link_stores_session_and_redirects_to_profile(): void
    {
        $userPayload = $this->fakeUserPayload();

        Http::fake([
            '*/auth/google/mobile/consume' => Http::response([
                'token' => 'fake-bearer-token-123',
                'user' => $userPayload,
            ], 200),
        ]);

        $response = $this->get('/auth/google/callback?state=fake-uuid-state');

        $response->assertRedirect(route('profile'));
        $this->assertEquals('fake-bearer-token-123', session('compay_token'));
        $this->assertInstanceOf(UserDTO::class, session('compay_user'));
    }

    public function test_google_callback_deep_link_redirects_home_without_state(): void
    {
        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('home'));
        $this->assertNull(session('compay_token'));
    }

    public function test_google_callback_deep_link_redirects_home_on_error(): void
    {
        Http::fake([
            '*/auth/google/mobile/consume' => Http::response([
                'error' => 'OAuth state inválido o expirado.',
            ], 404),
        ]);

        $response = $this->get('/auth/google/callback?state=expired-state');

        $response->assertRedirect(route('home'));
        $this->assertNull(session('compay_token'));
    }
}
