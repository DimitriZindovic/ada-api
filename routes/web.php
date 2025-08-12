<?php

use App\Http\Controllers\BikeController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('bikes', BikeController::class)->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('rents', RentController::class)->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);


