<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Route pour vider le cache - accessible sans authentification mais sécurisée
Route::post('/clear-cache', function (Request $request) {
    // Validation avec un token secret dans l'environment
    $token = $request->header('X-Cache-Token') ?? $request->input('token');
    $expectedToken = env('CACHE_CLEAR_TOKEN', 'your-secret-token-here');
    
    if ($token !== $expectedToken) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    try {
        // Méthodes qui fonctionnent en production sur Render
        \Illuminate\Support\Facades\Cache::flush();
        \Illuminate\Support\Facades\Config::clearResolvedInstances();
        
        // Si possible, essayer aussi Artisan
        if (function_exists('artisan')) {
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
        }
        
        return response()->json([
            'message' => 'Cache cleared successfully',
            'timestamp' => now()->toISOString()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to clear cache',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    //MESSAGE
    Route::get('/messages/{group}', [MessageController::class, 'index']);
    Route::post('/message/{group}', [MessageController::class, 'store']);

    //GROUP
    Route::apiResource('groups', GroupController::class)->only(['index', 'store', 'show', 'update']);
    Route::put('/group/{group}/leave', [GroupController::class, 'leave']);
});
