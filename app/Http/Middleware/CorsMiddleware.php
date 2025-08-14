<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->header('Origin');
        $allowedOrigins = explode(',', env('CORS_ALLOWED_ORIGINS', '*'));
        
        // Pour preflight OPTIONS requests
        if ($request->getMethod() === "OPTIONS") {
            $headers = [
                'Access-Control-Allow-Origin' => $this->isAllowed($origin, $allowedOrigins) ? $origin : $allowedOrigins[0],
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, Accept, Origin',
                'Access-Control-Max-Age' => '86400',
                'Access-Control-Allow-Credentials' => 'true'
            ];

            return response()->json([], 200, $headers);
        }

        $response = $next($request);

        // Ajouter les headers CORS à toutes les réponses
        $response->headers->set('Access-Control-Allow-Origin', $this->isAllowed($origin, $allowedOrigins) ? $origin : $allowedOrigins[0]);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }

    private function isAllowed($origin, $allowedOrigins)
    {
        if (in_array('*', $allowedOrigins)) {
            return true;
        }

        return in_array($origin, $allowedOrigins);
    }
}
