<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocationSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si la ubicación está en la sesión
        $hasLocation = $request->session()->has('location_province') && 
                       $request->session()->has('location_municipality');

        if (! $hasLocation) {
            // Compartir la bandera con Inertia para que el frontend sepa que debe abrir el modal
            Inertia::share('showLocationModal', true);
        }

        return $next($request);
    }
}
