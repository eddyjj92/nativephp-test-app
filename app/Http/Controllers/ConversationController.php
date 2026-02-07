<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\JsonResponse;
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

    public function show(Request $request, string $id): Response
    {
        $token = $request->session()->get('compay_token');

        if (! $token) {
            return $this->renderNoCache($request, 'Chat', [
                'conversation' => null,
                'messages' => [],
            ]);
        }

        try {
            $service = $this->service->setToken($token);
            $response = $service->getChatConversation($id);

            try {
                $service->markConversationAsRead($id);
            } catch (\Throwable) {
                // Non-critical: don't block page load if marking fails
            }

            return $this->renderNoCache($request, 'Chat', [
                'conversation' => $response['conversation'] ?? null,
                'messages' => $response['messages'] ?? [],
            ]);
        } catch (\Throwable $e) {
            return $this->renderNoCache($request, 'Chat', [
                'conversation' => null,
                'messages' => [],
                'error' => 'Error al cargar la conversación.',
            ]);
        }
    }

    public function messages(Request $request, string $id): JsonResponse
    {
        $token = $request->session()->get('compay_token');

        if (! $token) {
            return response()->json(['error' => 'No autenticado.'], 401);
        }

        try {
            $response = $this->service
                ->setToken($token)
                ->getChatConversation($id, $request->only(['page']));

            return response()->json($response['messages'] ?? []);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al cargar los mensajes.'], 500);
        }
    }

    public function sendMessage(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'receiver_id' => ['required', 'integer'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $token = $request->session()->get('compay_token');

        if (! $token) {
            return response()->json(['error' => 'No autenticado.'], 401);
        }

        try {
            $response = $this->service
                ->setToken($token)
                ->sendChatMessage(
                    conversationId: (int) $id,
                    receiverId: $request->integer('receiver_id'),
                    message: $request->string('message')->toString(),
                );

            return response()->json($response);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al enviar el mensaje.'], 500);
        }
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $token = $request->session()->get('compay_token');

        if (! $token) {
            return response()->json(['error' => 'No autenticado.'], 401);
        }

        try {
            $response = $this->service
                ->setToken($token)
                ->markConversationAsRead($id);

            return response()->json($response);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al marcar como leído.'], 500);
        }
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
