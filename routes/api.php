<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    return response()->json(['message' => 'Cache cleared']);
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
