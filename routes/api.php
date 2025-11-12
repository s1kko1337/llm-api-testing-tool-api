<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Email\EmailVerificationController;
use App\Http\Controllers\Api\V1\Password\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::post('/forgot-password', [PasswordResetController::class, 'revoke']);
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'invoke'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);

        Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
            ->name('verification.notice');

        Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
            ->name('verification.send');

        Route::middleware('role:admin')->group(function () {});
        Route::middleware('role:user')->group(function () {});
    });
});
