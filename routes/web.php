<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataValidationController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\JobApplicationController;

// Home page
Route::get('/', function () {
    return view('home');
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Data Validation Routes
Route::get('/data-validation/create', [DataValidationController::class, 'create'])->name('data-validation.create');
Route::post('/data-validation', [DataValidationController::class, 'store'])->name('data-validation.store');
Route::get('/data-validation/progress', [DataValidationController::class, 'progress'])->name('data-validation.progress');
Route::get('/data-validation/results', [DataValidationController::class, 'results'])->name('data-validation.results');

// Job Vacancies Routes
Route::get('/job-vacancies', [JobVacancyController::class, 'index'])->name('job-vacancies.index');
Route::get('/job-vacancies/{id}', [JobVacancyController::class, 'show'])->name('job-vacancies.show');

// Job Applications Routes
Route::get('/job-applications', [JobApplicationController::class, 'index'])->name('job-applications.index');
Route::get('/job-applications/create/{vacancyId}', [JobApplicationController::class, 'create'])->name('job-applications.create');
Route::post('/job-applications', [JobApplicationController::class, 'store'])->name('job-applications.store');
Route::get('/job-applications/{id}', [JobApplicationController::class, 'show'])->name('job-applications.show');

// AJAX Routes for dashboard (session-based authentication)
Route::middleware(['web'])->prefix('ajax')->group(function () {
    Route::get('/user-status', [AuthController::class, 'ajaxUserStatus'])->name('ajax.user-status');
    Route::get('/validations', [DataValidationController::class, 'ajaxIndex'])->name('ajax.validations');
    Route::get('/applications', [JobApplicationController::class, 'ajaxIndex'])->name('ajax.applications');
    Route::get('/job-categories', [DataValidationController::class, 'ajaxJobCategories'])->name('ajax.job-categories');
    Route::post('/validation', [DataValidationController::class, 'ajaxStore'])->name('ajax.validation.store');
    Route::get('/job-vacancies', [JobVacancyController::class, 'ajaxIndex'])->name('ajax.job-vacancies');
    Route::get('/job-vacancies/{id}', [JobVacancyController::class, 'ajaxShow'])->name('ajax.job-vacancy');
});

// Debug auth guard route
Route::get('/debug-auth', function () {
    try {
        // Try to access the auth system
        $defaultGuard = 'web'; // Use default web guard
        $availableGuards = array_keys(config('auth.guards', []));

        return response()->json([
            'success' => true,
            'default_guard' => $defaultGuard,
            'available_guards' => $availableGuards,
            'session' => [
                'user_logged_in' => Session::has('user_logged_in'),
                'user_id' => Session::get('user_id')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test route to debug society guard issue
Route::get('/test-auth', function () {
    return response()->json([
        'session_logged_in' => Session::has('user_logged_in'),
        'user_id' => Session::get('user_id'),
        'default_guard' => config('auth.defaults.guard'),
        'available_guards' => array_keys(config('auth.guards', [])),
        'message' => 'Test route working'
    ]);
});

// Test route for user status endpoint
Route::get('/test-user-status', function () {
    try {
        // Test the user status endpoint directly
        $controller = new \App\Http\Controllers\AuthController();
        $request = new \Illuminate\Http\Request();

        // Test without vacancy ID
        $response1 = $controller->ajaxUserStatus($request);

        // Test with vacancy ID
        $request->merge(['vacancy_id' => 1]);
        $response2 = $controller->ajaxUserStatus($request);

        return response()->json([
            'test1_without_vacancy' => $response1->getData(),
            'test2_with_vacancy' => $response2->getData(),
            'message' => 'User status endpoint test completed'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test page for user status
Route::get('/test-user-status-page', function () {
    return view('test-user-status');
});

// Debug test page
Route::get('/debug-test', function () {
    return view('debug-test');
});

// Test vacancy show page
Route::get('/test-vacancy-show', function () {
    return view('test-vacancy-show');
});

// Simple test page
Route::get('/simple-test', function () {
    return view('simple-test');
});

// Test API Routes (temporary for testing)
Route::prefix('api/v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
    });

    // Data Validation API routes
    Route::post('/validation', [App\Http\Controllers\Api\ValidationController::class, 'store']);
    Route::get('/validations', [App\Http\Controllers\Api\ValidationController::class, 'index']);
    Route::get('/validations/history', [App\Http\Controllers\Api\ValidationController::class, 'history']);

    // Job Categories API route
    Route::get('/job-categories', [App\Http\Controllers\Api\JobCategoryController::class, 'index']);

    // Job Vacancies API routes
    Route::get('/job_vacancies', [App\Http\Controllers\Api\JobVacancyApiController::class, 'index']);
    Route::get('/job_vacancies/{id}', [App\Http\Controllers\Api\JobVacancyApiController::class, 'show']);

    // Job Applications API routes
    Route::get('/applications', [App\Http\Controllers\Api\JobApplicationApiController::class, 'index']);
    Route::get('/applications/{id}', [App\Http\Controllers\Api\JobApplicationApiController::class, 'show']);
    Route::post('/applications', [App\Http\Controllers\Api\JobApplicationApiController::class, 'store']);
});
