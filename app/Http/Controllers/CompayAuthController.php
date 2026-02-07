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
            'redirect_to' => 'nullable|string',
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

            $redirectTo = $validated['redirect_to'] ?? null;

            return $redirectTo ? redirect($redirectTo) : back();
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

    public function beneficiaries(Request $request, CompayMarketService $service)
    {
        $user = $request->session()->get('compay_user');
        $token = $request->session()->get('compay_token');

        if (! $user || ! $token) {
            return redirect()->route('home');
        }

        $params = $request->only(['order_by', 'page', 'per_page', 'province_id']);

        try {
            $response = $service->setToken($token)->getBeneficiaries($params);

            $beneficiaries = $response['beneficiaries'] ?? [];

            // Build local next page URL for pagination
            $nextPageUrl = null;
            if (! empty($beneficiaries['next_page_url'])) {
                $nextPage = $beneficiaries['current_page'] + 1;
                $nextPageUrl = route('beneficiaries', ['page' => $nextPage]);
            }

            return \Inertia\Inertia::render('Beneficiaries', [
                'beneficiaries' => \Inertia\Inertia::merge($beneficiaries['data'] ?? []),
                'beneficiariesNextPageUrl' => $nextPageUrl,
                'currentPage' => $beneficiaries['current_page'] ?? 1,
                'lastPage' => $beneficiaries['last_page'] ?? 1,
                'total' => $beneficiaries['total'] ?? 0,
            ]);
        } catch (\Exception $e) {
            return \Inertia\Inertia::render('Beneficiaries', [
                'beneficiaries' => [],
                'beneficiariesNextPageUrl' => null,
                'currentPage' => 1,
                'lastPage' => 1,
                'total' => 0,
                'error' => 'Error al cargar los beneficiarios.',
            ]);
        }
    }

    public function beneficiaryCreate(Request $request, CompayMarketService $service)
    {
        $user = $request->session()->get('compay_user');
        $token = $request->session()->get('compay_token');

        if (! $user || ! $token) {
            return redirect()->route('home');
        }

        // Get provinces for the municipality selector
        $provinces = $service->getProvinces('active', cache: true);

        return \Inertia\Inertia::render('BeneficiaryCreate', [
            'provinces' => $provinces,
        ]);
    }

    public function beneficiaryStore(Request $request, CompayMarketService $service)
    {
        $user = $request->session()->get('compay_user');
        $token = $request->session()->get('compay_token');

        if (! $user || ! $token) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'municipality_id' => 'required|integer',
        ]);

        try {
            $response = $service->setToken($token)->createBeneficiary($validated);

            if (isset($response['beneficiary'])) {
                return redirect()->route('beneficiaries')->with('success', 'Beneficiario creado exitosamente.');
            }

            return back()->withErrors(['error' => $response['message'] ?? 'Error al crear el beneficiario.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear el beneficiario.']);
        }
    }

    public function beneficiaryEdit(Request $request, int $id, CompayMarketService $service)
    {
        $user = $request->session()->get('compay_user');
        $token = $request->session()->get('compay_token');

        if (! $user || ! $token) {
            return redirect()->route('home');
        }

        try {
            $response = $service->setToken($token)->getBeneficiary($id);
            $beneficiary = $response['beneficiary'] ?? null;

            if (! $beneficiary) {
                return redirect()->route('beneficiaries')->withErrors(['error' => 'Beneficiario no encontrado.']);
            }

            $provinces = $service->getProvinces('active', cache: true);

            return \Inertia\Inertia::render('BeneficiaryEdit', [
                'beneficiary' => $beneficiary,
                'provinces' => $provinces,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('beneficiaries')->withErrors(['error' => 'Error al cargar el beneficiario.']);
        }
    }

    public function beneficiaryUpdate(Request $request, int $id, CompayMarketService $service)
    {
        $user = $request->session()->get('compay_user');
        $token = $request->session()->get('compay_token');

        if (! $user || ! $token) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'municipality_id' => 'required|integer',
        ]);

        try {
            $response = $service->setToken($token)->updateBeneficiary($id, $validated);

            if (isset($response['beneficiary'])) {
                return redirect()->route('beneficiaries')->with('success', 'Beneficiario actualizado exitosamente.');
            }

            return back()->withErrors(['error' => $response['message'] ?? 'Error al actualizar el beneficiario.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar el beneficiario.']);
        }
    }

    public function beneficiaryDestroy(Request $request, int $id, CompayMarketService $service)
    {
        $user = $request->session()->get('compay_user');
        $token = $request->session()->get('compay_token');

        if (! $user || ! $token) {
            return redirect()->route('home');
        }

        try {
            $service->setToken($token)->deleteBeneficiary($id);

            return redirect()->route('beneficiaries')->with('success', 'Beneficiario eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el beneficiario.']);
        }
    }
}
