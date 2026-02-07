<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BroadcastingAuthController extends Controller
{
    /**
     * Proxy la autenticación de broadcasting al backend externo de Compay Market.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $token = $request->session()->get('compay_token');

        if (! $token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // NativePHP's PHPBridge may not set CONTENT_TYPE correctly,
        // causing $request->input() to return null. Fall back to
        // parsing the raw body when that happens.
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');

        if (! $socketId || ! $channelName) {
            parse_str($request->getContent(), $parsed);
            $socketId = $socketId ?: ($parsed['socket_id'] ?? null);
            $channelName = $channelName ?: ($parsed['channel_name'] ?? null);
        }

        if (! $socketId || ! $channelName) {
            return response()->json(['message' => 'Missing socket_id or channel_name.'], 422);
        }

        $response = Http::baseUrl(config('services.compay_market.api_base_url'))
            ->withToken($token)
            ->acceptJson()
            ->post('/broadcasting/auth', [
                'socket_id' => $socketId,
                'channel_name' => $channelName,
            ]);

        return response()->json($response->json(), $response->status());
    }
}
