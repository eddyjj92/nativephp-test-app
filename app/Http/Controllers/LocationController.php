<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controlador para gestionar la ubicación de entrega del usuario.
 * Maneja la obtención de datos geográficos desde la API y su persistencia en la sesión.
 */
class LocationController extends Controller
{
    /**
     * Crea una nueva instancia del controlador.
     *
     * @param  CompayMarketService  $compayMarketService  Servicio de la API de Compay Market.
     */
    public function __construct(
        protected CompayMarketService $compayMarketService
    ) {}

    /**
     * Obtiene la lista de provincias y municipios desde la API real.
     * Filtra solo por provincias con estado 'active'.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $provinces = $this->compayMarketService->getProvinces('active');

            $municipalities = [];
            foreach ($provinces as $province) {
                foreach ($province->municipalities as $muni) {
                    $municipalities[] = $muni;
                }
            }

            return response()->json([
                'provinces' => $provinces,
                'municipalities' => $municipalities,
            ]);
        } catch (\Exception $e) {
            // Log error or handle gracefully
            return response()->json([
                'provinces' => [],
                'municipalities' => [],
                'error' => 'Failed to load location data'.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Guarda los objetos DTO de la provincia y municipio seleccionados en la sesión.
     * Valida la existencia de los IDs contra los datos reales de la API.
     *
     * @param  Request  $request  Petición con IDs de provincia y municipio.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'province' => 'required',
            'municipality' => 'required',
        ]);

        try {
            $provinces = $this->compayMarketService->getProvinces('active');

            $province = collect($provinces)->first(fn ($p) => $p->id == $validated['province']);
            $municipality = null;

            if ($province) {
                $municipality = collect($province->municipalities)->first(fn ($m) => $m->id == $validated['municipality']);
            }

            if ($province && $municipality) {
                $request->session()->put('selected_province', $province);
                $request->session()->put('selected_municipality', $municipality);
            }
        } catch (\Exception $e) {
            // If API fails during validation, we can't verify the location.
            // Option: Allow it blindly OR fail. Failing is safer to prevent invalid state.
            return back()->withErrors(['location' => 'Unable to verify location settings. Please try again.']);
        }

        return back();
    }
}
