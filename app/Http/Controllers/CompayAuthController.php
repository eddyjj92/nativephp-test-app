<?php

namespace App\Http\Controllers;

use App\Services\CompayMarketService;
use Illuminate\Http\Request;

class CompayAuthController extends Controller
{
    public function login(Request $request, CompayMarketService $service)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $response = $service->login($validated['email'], $validated['password']);

            if (! $response->user->isCustomer()) {
                return back()->withErrors([
                    'email' => 'Esta cuenta no tiene acceso a la aplicación. Solo los clientes pueden iniciar sesión.',
                ]);
            }

            $request->session()->put('compay_token', $response->token);
            $request->session()->put('compay_user', $response->user);

            return back();
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.']);
        }
    }

    public function updateProfile(Request $request, CompayMarketService $service)
    {
        $user = $request->session()->get('compay_user');
        $token = $request->session()->get('compay_token');

        if (! $user || ! $token) {
            return back()->withErrors(['error' => 'No autenticado.']);
        }

        // Solo permitimos ciertos campos para la actualización simple desde el perfil
        $data = $request->only(['name', 'email', 'avatar', 'phone_country_code', 'phone', 'address']);

        try {
            $response = $service->setToken($token)->updateUser($user->id, $data);

            if (isset($response['user'])) {
                // Actualizamos el usuario en la sesión
                $updatedUser = \App\DTOs\UserDTO::fromArray($response['user']);
                $request->session()->put('compay_user', $updatedUser);
            }

            return back();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar el perfil.']);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['compay_token', 'compay_user']);

        return redirect()->route('home');
    }

    public function orders(Request $request, CompayMarketService $service)
    {
        $user = $request->session()->get('compay_user');
        $token = $request->session()->get('compay_token');

        if (! $user || ! $token) {
            return redirect()->route('home');
        }

        $params = $request->only(['beneficiary_id', 'delivery_type_id', 'order_by', 'page', 'per_page', 'province_id', 'status']);

        try {
            $response = $service->setToken($token)->getOrders($params);

            return \Inertia\Inertia::render('Orders', [
                'orders' => $response['orders'] ?? [],
            ]);
        } catch (\Exception $e) {
            return \Inertia\Inertia::render('Orders', [
                'orders' => [],
                'error' => 'Error al cargar los pedidos.',
            ]);
        }
    }
}
