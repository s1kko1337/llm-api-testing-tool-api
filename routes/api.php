<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);

        Route::middleware('role:admin')->group(function () {});
        Route::middleware('role:user')->group(function () {});
    });

});
