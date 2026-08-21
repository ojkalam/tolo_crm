<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ActivityController;
use App\Http\Controllers\Api\V1\AnalyticsDashboardController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DealController;
use App\Http\Controllers\Api\V1\GlobalSearchController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\PipelineController;
use App\Http\Controllers\Api\V1\TimelineController;
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
        Route::post('contacts/import', [ContactController::class, 'import']);
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('contacts', ContactController::class);

        Route::post('leads/{lead}/convert', [LeadController::class, 'convert']);
        Route::apiResource('leads', LeadController::class);

        // Pipelines & Kanban Board
        Route::get('pipelines/{pipeline}/kanban', [PipelineController::class, 'kanban']);
        Route::post('pipelines/{pipeline}/reorder-stages', [PipelineController::class, 'reorderStages']);
        Route::apiResource('pipelines', PipelineController::class);

        // Deals & Stage Transitions
        Route::get('deals/export', [DealController::class, 'export']);
        Route::post('deals/{deal}/move-stage', [DealController::class, 'moveStage']);
        Route::apiResource('deals', DealController::class);

        // Activities & Tasks
        Route::post('activities/{activity}/complete', [ActivityController::class, 'complete']);
        Route::apiResource('activities', ActivityController::class);

        // Unified Timeline Stream
        Route::get('timeline', [TimelineController::class, 'index']);

        // Global Full-Text Search
        Route::get('search', [GlobalSearchController::class, 'search']);

        // Analytics & Reports
        Route::prefix('analytics')->group(function () {
            Route::get('dashboard', [AnalyticsDashboardController::class, 'dashboard']);
            Route::get('pipeline-forecast', [AnalyticsDashboardController::class, 'pipelineForecast']);
            Route::get('rep-performance', [AnalyticsDashboardController::class, 'repPerformance']);
        });
    });
});
