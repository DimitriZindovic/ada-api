<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware pour gérer automatiquement les requêtes OPTIONS
        $middleware->api(prepend: [
            function ($request, $next) {
                if ($request->getMethod() === 'OPTIONS') {
                    return response('', 200)
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                        ->header('Access-Control-Max-Age', '86400');
                }
                
                $response = $next($request);
                $response->headers->set('Access-Control-Allow-Origin', '*');
                return $response;
            }
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
