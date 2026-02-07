<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ConversationControllerTest extends TestCase
{
    public function test_conversations_index_returns_data_from_api_for_authenticated_session(): void
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
                'settings' => $this->fakeSettings(),
            ], 200),
            '*/chat/conversations*' => Http::response([
                'conversations' => [
                    'current_page' => 1,
                    'data' => [
                        [
                            'id' => 14,
                            'type' => 'private',
                            'unread_count' => 7,
                            'user_one' => [
                                'id' => 4,
                                'name' => 'Pedro Perez',
                                'avatar' => 'https://example.com/pedro.png',
                            ],
                            'user_two' => [
                                'id' => 385,
                                'name' => 'Eddy Javier',
                                'avatar' => 'https://example.com/eddy.png',
                            ],
                            'last_message' => [
                                'content' => 'Hola',
                            ],
                        ],
                    ],
                ],
                'is_staff' => false,
            ], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'token-test',
            'compay_user' => (object) ['id' => 385],
        ])->get(route('conversations'));

        $response->assertOk();
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertNotNull($cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Conversations')
            ->where('isStaff', false)
            ->has('conversations', 1)
            ->where('conversations.0.id', 14)
            ->where('conversations.0.unread_count', 7)
        );

        Http::assertSent(fn ($request) => str_contains($request->url(), '/chat/conversations'));
    }

    public function test_conversations_index_returns_empty_when_no_token(): void
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
                'settings' => $this->fakeSettings(),
            ], 200),
        ]);

        $response = $this->get(route('conversations'));

        $response->assertOk();
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertNotNull($cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Conversations')
            ->where('isStaff', false)
            ->where('conversations', [])
        );
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
