<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\EnsureLocationSelected::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle Inertia requests - return JSON for connection/server errors
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->header('X-Inertia')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                // Ignore validation errors - let Laravel handle them normally (redirect back with errors)
                if ($status === 422) {
                    return null;
                }

                // For server errors, return a response that frontend can handle
                if ($status >= 500) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Error del servidor',
                        'status' => $status,
                    ], $status)->header('X-Inertia-Error', 'true');
                }
            }

            return null; // Let Laravel handle other exceptions normally
        });
    })->create();
