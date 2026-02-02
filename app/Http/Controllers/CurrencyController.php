<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct(
        protected CompayMarketService $compayMarketService
    ) {}

    /**
     * Guarda la moneda seleccionada en la sesión.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'currency_id' => 'required|integer',
        ]);

        try {
            $currencies = $this->compayMarketService->getCurrencies(cache: true);

            $currency = collect($currencies)->first(fn ($c) => $c->id == $validated['currency_id']);

            if ($currency) {
                $request->session()->put('selected_currency', $currency);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['currency' => 'Unable to set currency. Please try again.']);
        }

        return back();
    }
}
