<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        protected CompayMarketService $compayMarketService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'currency' => ['required', 'string', 'size:3'],
            'beneficiary_id' => ['required', 'integer', 'min:1'],
            'delivery_type_id' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $token = $request->session()->get('compay_token');
        if (! $token) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }

        $cartSession = $request->session()->get('cart', []);
        if (empty($cartSession)) {
            return response()->json([
                'message' => 'El carrito está vacío.',
            ], 422);
        }

        $cart = collect($cartSession)
            ->map(function (mixed $item, string|int $productId): array {
                $id = is_array($item) && isset($item['id']) ? (int) $item['id'] : (int) $productId;
                $quantity = is_array($item) && isset($item['quantity']) ? (int) $item['quantity'] : 1;

                return [
                    'id' => $id,
                    'quantity' => max(1, $quantity),
                ];
            })
            ->values()
            ->all();

        try {
            $response = $this->compayMarketService
                ->setToken($token)
                ->createHostedCheckoutOrder(
                    currency: strtoupper($validated['currency']),
                    beneficiaryId: (int) $validated['beneficiary_id'],
                    deliveryTypeId: (int) $validated['delivery_type_id'],
                    notes: (string) ($validated['notes'] ?? ''),
                    cart: $cart,
                );
        } catch (Throwable) {
            return response()->json([
                'message' => 'No se pudo crear la orden en este momento.',
            ], 502);
        }

        $redirectUrl = $response['order']['redirect_url'] ?? null;
        if (! is_string($redirectUrl) || $redirectUrl === '') {
            return response()->json([
                'message' => 'La orden fue creada pero no devolvió una URL de pago válida.',
                'order' => $response['order'] ?? null,
            ], 502);
        }

        return response()->json([
            'message' => (string) ($response['message'] ?? 'Orden creada exitosamente'),
            'order' => $response['order'] ?? null,
            'redirect_url' => $redirectUrl,
        ]);
    }
}
