<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function __construct(
        protected CompayMarketService $compayMarketService
    ) {}

    /**
     * Agrega un producto a favoritos en la sesión.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $favorites = session()->get('favorites', []);
        $productId = $validated['product_id'];

        if (!isset($favorites[$productId])) {
            $favorites[$productId] = [
                'id' => $productId,
            ];
        }

        session()->put('favorites', $favorites);

        return back()->with('success', 'Producto agregado a favoritos');
    }

    /**
     * Elimina un producto de favoritos.
     */
    public function destroy(int $productId): RedirectResponse
    {
        $favorites = session()->get('favorites', []);

        if (isset($favorites[$productId])) {
            unset($favorites[$productId]);
            session()->put('favorites', $favorites);
        }

        return back();
    }

    /**
     * Vacía la lista de favoritos.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('favorites');

        return back();
    }
}
