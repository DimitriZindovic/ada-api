<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route pour vider le cache en production avec sécurité
Route::post('/clear-cache', function (Request $request) {
    // Vérification simple par IP ou token basique
    $token = $request->input('token');
    if ($token !== 'clear-cache-ada-2025') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    try {
        \Illuminate\Support\Facades\Cache::flush();
        return response()->json([
            'message' => 'Cache cleared successfully',
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
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
