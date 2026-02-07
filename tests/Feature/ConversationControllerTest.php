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

    public function test_chat_show_returns_conversation_and_messages(): void
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
            '*/chat/conversations/14/read' => Http::response([
                'success' => true,
                'messages_marked' => 2,
            ], 200),
            '*/chat/conversations/14' => Http::response([
                'conversation' => [
                    'id' => 14,
                    'user_one_id' => 4,
                    'user_two_id' => 385,
                    'type' => 'private',
                    'last_message_at' => '2025-01-15T10:00:00Z',
                    'created_at' => '2025-01-10T08:00:00Z',
                    'updated_at' => '2025-01-15T10:00:00Z',
                    'user_one' => ['id' => 4, 'name' => 'Pedro', 'avatar' => null],
                    'user_two' => ['id' => 385, 'name' => 'Eddy', 'avatar' => null],
                ],
                'messages' => [
                    'data' => [
                        $this->fakeMessage(1, 14, 4, 'Hola, cómo estás?'),
                        $this->fakeMessage(2, 14, 385, 'Bien, gracias!'),
                    ],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 2,
                    'next_page_url' => null,
                ],
            ], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'token-test',
            'compay_user' => (object) ['id' => 385],
        ])->get(route('chat.show', ['id' => 14]));

        $response->assertOk();
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertNotNull($cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Chat')
            ->has('conversation')
            ->where('conversation.id', 14)
            ->where('conversation.type', 'private')
            ->has('messages.data', 2)
            ->where('messages.data.0.content', 'Hola, cómo estás?')
            ->where('messages.data.1.content', 'Bien, gracias!')
        );

        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && str_contains($request->url(), '/chat/conversations/14/read'));
    }

    public function test_chat_show_still_works_when_mark_as_read_fails(): void
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
            '*/chat/conversations/14/read' => Http::response([], 500),
            '*/chat/conversations/14' => Http::response([
                'conversation' => [
                    'id' => 14,
                    'user_one_id' => 4,
                    'user_two_id' => 385,
                    'type' => 'private',
                    'user_one' => ['id' => 4, 'name' => 'Pedro', 'avatar' => null],
                    'user_two' => ['id' => 385, 'name' => 'Eddy', 'avatar' => null],
                ],
                'messages' => [
                    'data' => [
                        $this->fakeMessage(1, 14, 4, 'Hola'),
                    ],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 1,
                    'next_page_url' => null,
                ],
            ], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'token-test',
            'compay_user' => (object) ['id' => 385],
        ])->get(route('chat.show', ['id' => 14]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Chat')
            ->where('conversation.id', 14)
            ->has('messages.data', 1)
        );
    }

    public function test_messages_endpoint_returns_paginated_messages(): void
    {
        Http::fake([
            '*/chat/conversations/14*' => Http::response([
                'conversation' => [
                    'id' => 14,
                    'user_one_id' => 4,
                    'user_two_id' => 385,
                    'type' => 'private',
                ],
                'messages' => [
                    'data' => [
                        $this->fakeMessage(1, 14, 4, 'Mensaje antiguo'),
                    ],
                    'current_page' => 2,
                    'last_page' => 2,
                    'per_page' => 10,
                    'total' => 11,
                    'next_page_url' => null,
                ],
            ], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'token-test',
        ])->getJson('/conversations/14/messages?page=2');

        $response->assertOk();
        $response->assertJsonPath('data.0.content', 'Mensaje antiguo');
        $response->assertJsonPath('current_page', 2);
    }

    public function test_messages_endpoint_returns_401_when_no_token(): void
    {
        $response = $this->getJson('/conversations/14/messages');

        $response->assertStatus(401);
    }

    public function test_chat_show_returns_empty_when_no_token(): void
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

        $response = $this->get(route('chat.show', ['id' => 14]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Chat')
            ->where('conversation', null)
            ->where('messages', [])
        );
    }

    public function test_send_message_posts_to_api(): void
    {
        Http::fake([
            '*/chat/message' => Http::response([
                'conversation' => '14',
                'message' => 'Hola mundo',
            ], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'token-test',
        ])->postJson('/conversations/14/messages', [
            'receiver_id' => 4,
            'message' => 'Hola mundo',
        ]);

        $response->assertOk();
        $response->assertJson([
            'conversation' => '14',
            'message' => 'Hola mundo',
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/chat/message')
                && $request['conversation_id'] === 14
                && $request['receiver_id'] === 4
                && $request['message'] === 'Hola mundo';
        });
    }

    public function test_send_message_returns_401_when_no_token(): void
    {
        $response = $this->postJson('/conversations/14/messages', [
            'receiver_id' => 4,
            'message' => 'Hola',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'No autenticado.']);
    }

    public function test_send_message_validates_input(): void
    {
        $response = $this->withSession([
            'compay_token' => 'token-test',
        ])->postJson('/conversations/14/messages', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['receiver_id', 'message']);
    }

    public function test_mark_as_read_sends_patch_to_api(): void
    {
        Http::fake([
            '*/chat/conversations/14/read' => Http::response([
                'success' => true,
                'messages_marked' => 3,
            ], 200),
        ]);

        $response = $this->withSession([
            'compay_token' => 'token-test',
        ])->patchJson('/conversations/14/read');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'messages_marked' => 3,
        ]);

        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && str_contains($request->url(), '/chat/conversations/14/read'));
    }

    public function test_mark_as_read_returns_401_when_no_token(): void
    {
        $response = $this->patchJson('/conversations/14/read');

        $response->assertStatus(401);
        $response->assertJson(['error' => 'No autenticado.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function fakeMessage(int $id, int $conversationId, int $senderId, string $content): array
    {
        return [
            'id' => $id,
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'content' => $content,
            'type' => 'text',
            'file_path' => null,
            'file_name' => null,
            'read_at' => null,
            'created_at' => '2025-01-15T10:00:00Z',
            'updated_at' => '2025-01-15T10:00:00Z',
            'sender' => ['id' => $senderId, 'name' => 'Test User', 'avatar' => null],
        ];
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
