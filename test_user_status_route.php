<?php

use Illuminate\Support\Facades\Route;

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
