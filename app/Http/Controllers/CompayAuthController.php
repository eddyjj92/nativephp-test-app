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
            
            $request->session()->put('compay_token', $response->token);
            $request->session()->put('compay_user', $response->user);
            
            return back();
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.']);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['compay_token', 'compay_user']);
        return redirect()->route('home');
    }
}
