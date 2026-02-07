<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ConversationController extends Controller
{
    public function __construct(protected CompayMarketService $service) {}

    public function index(Request $request): Response
    {
        $token = $request->session()->get('compay_token');

        if (! $token) {
            return $this->renderNoCache($request, 'Conversations', [
                'conversations' => [],
                'isStaff' => false,
            ]);
        }

        try {
            $response = $this->service
                ->setToken($token)
                ->getChatConversations($request->only(['page', 'per_page']));

            return $this->renderNoCache($request, 'Conversations', [
                'conversations' => $response['conversations']['data'] ?? [],
                'isStaff' => (bool) ($response['is_staff'] ?? false),
            ]);
        } catch (\Throwable $e) {
            return $this->renderNoCache($request, 'Conversations', [
                'conversations' => [],
                'isStaff' => false,
                'error' => 'Error al cargar las conversaciones.',
            ]);
        }
    }

    public function show(string $id): Response
    {
        return Inertia::render('Chat', [
            'id' => $id,
        ])->toResponse(request());
    }

    /**
     * @param  array<string, mixed>  $props
     */
    private function renderNoCache(Request $request, string $component, array $props): Response
    {
        $response = Inertia::render($component, $props)->toResponse($request);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
