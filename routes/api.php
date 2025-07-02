<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ValidationController;
use App\Http\Controllers\Api\JobCategoryController;
use App\Http\Controllers\Api\JobVacancyApiController;
use App\Http\Controllers\Api\JobApplicationApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 routes
Route::prefix('v1')->group(function () {

    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Data Validation routes
    Route::post('/validation', [ValidationController::class, 'store']);
    Route::get('/validations', [ValidationController::class, 'index']);
    Route::get('/validations/history', [ValidationController::class, 'history']);

    // Job Categories routes
    Route::get('/job-categories', [JobCategoryController::class, 'index']);

    // Job Vacancies routes
    Route::get('/job_vacancies', [JobVacancyApiController::class, 'index']);
    Route::get('/job_vacancies/category/{categoryId}', [JobVacancyApiController::class, 'byCategory']);
    Route::get('/job_vacancies/{id}', [JobVacancyApiController::class, 'show']);

    // Job Applications routes
    Route::post('/applications', [JobApplicationApiController::class, 'store']);
    Route::get('/applications', [JobApplicationApiController::class, 'index']);

});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found'
    ], 404);
});
