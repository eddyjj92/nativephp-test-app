<?php

namespace App\Http\Controllers;

use App\DTOs\MunicipalityDTO;
use App\DTOs\ProvinceDTO;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LocationController extends Controller
{
    /**
     * Get the list of provinces and municipalities.
     */
    public function index()
    {
        // Mock data - In a real app, this would come from a DB or API service
        $provinces = [
            new ProvinceDTO('havana', 'La Habana'),
            new ProvinceDTO('santiago', 'Santiago de Cuba'),
            new ProvinceDTO('matanzas', 'Matanzas'),
        ];

        $municipalities = [
            new MunicipalityDTO('plaza', 'Plaza de la Revolución', 'havana'),
            new MunicipalityDTO('playa', 'Playa', 'havana'),
            new MunicipalityDTO('centro_habana', 'Centro Habana', 'havana'),
            new MunicipalityDTO('santiago_muni', 'Santiago de Cuba', 'santiago'),
            new MunicipalityDTO('palma', 'Palma Soriano', 'santiago'),
            new MunicipalityDTO('matanzas_muni', 'Matanzas', 'matanzas'),
            new MunicipalityDTO('varadero', 'Varadero', 'matanzas'),
        ];

        return response()->json([
            'provinces' => $provinces,
            'municipalities' => $municipalities,
        ]);
    }

    /**
     * Save the selected location to the session.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'province' => 'required|string',
            'municipality' => 'required|string',
        ]);

        $request->session()->put('location_province', $validated['province']);
        $request->session()->put('location_municipality', $validated['municipality']);

        return back();
    }
}
