<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\LeadController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Auth Endpoints
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Authenticated Auth Endpoints
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::get('/profile', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::put('/password', [AuthController::class, 'updatePassword']);
        });
    });

    // Authenticated CRM Endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('contacts', ContactController::class);

        Route::post('leads/{lead}/convert', [LeadController::class, 'convert']);
        Route::apiResource('leads', LeadController::class);
    });
});
