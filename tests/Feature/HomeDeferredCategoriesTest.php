<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomeDeferredCategoriesTest extends TestCase
{
    public function test_home_categories_are_deferred_and_loaded_with_next_page_url(): void
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
            '*/categories*' => Http::response([
                'categories' => [
                    'data' => [
                        [
                            'id' => 3,
                            'name' => 'Carnes',
                            'slug' => 'carnes',
                            'description' => 'Carnes y embutidos',
                            'image' => 'https://example.com/category.png',
                            'created_at' => now()->toIso8601String(),
                            'updated_at' => now()->toIso8601String(),
                        ],
                    ],
                    'current_page' => 1,
                    'last_page' => 3,
                    'next_page_url' => 'https://api.example.com/categories?page=2',
                    'prev_page_url' => null,
                ],
            ], 200),
            '*/banners*' => Http::response([
                'banners' => [],
            ], 200),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->missing('categories')
            ->missing('categoriesNextPageUrl')
            ->loadDeferredProps(fn (Assert $deferredPage) => $deferredPage
                ->has('categories', 1)
                ->where('categories.0.id', 3)
                ->where('categories.0.name', 'Carnes')
                ->where('categoriesNextPageUrl', route('home', ['page' => 2]))
            )
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
