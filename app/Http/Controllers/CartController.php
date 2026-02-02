<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
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
}
