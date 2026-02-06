<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\TransportationCostRequest;
use App\Services\CompayMarketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CompayMarketService $compayMarketService
    ) {}

    /**
     * Agrega un producto al carrito en la sesión.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $productId = $validated['product_id'];

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $validated['quantity'];
        } else {
            $cart[$productId] = [
                'id' => $productId,
                'quantity' => $validated['quantity'],
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Producto agregado al carrito');
    }

    /**
     * Actualiza la cantidad de un producto en el carrito.
     */
    public function update(Request $request, int $productId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);
        }

        return back();
    }

    /**
     * Elimina un producto del carrito.
     */
    public function destroy(int $productId): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return back();
    }

    /**
     * Vacía el carrito.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return back();
    }

    /**
     * Obtiene el costo de transportación para el checkout.
     */
    public function transportationCost(TransportationCostRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $token = $request->session()->get('compay_token');

        if ($token) {
            $this->compayMarketService->setToken($token);
        }

        $response = $this->compayMarketService->getTransportationPriceForWeight(
            costRingId: (int) $validated['cost_ring_id'],
            weightKg: (float) $validated['weight_kg'],
            totalCost: array_key_exists('total_cost', $validated) ? (float) $validated['total_cost'] : null
        );

        return response()->json([
            'price' => (string) ($response['price'] ?? '0'),
            'price_with_discount' => (string) ($response['price_with_discount'] ?? '0'),
            'weight_range' => (string) ($response['weight_range'] ?? ''),
            'has_discount' => (bool) ($response['has_discount'] ?? false),
        ]);
    }
}
