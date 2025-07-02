<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Society;
use App\Models\Regional;

class AuthController extends Controller
{
    /**
     * Login as society
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'id_card_number' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find society by ID card number
            $society = Society::with('regional')->where('id_card_number', $request->id_card_number)->first();

            // Check if society exists and password is correct
            if (!$society || !Hash::check($request->password, $society->password)) {
                return response()->json([
                    'message' => 'ID Card Number or Password incorrect'
                ], 401);
            }

            // Generate authentication token (MD5 hash of ID card number + current timestamp)
            $token = md5($society->id_card_number . time());

            // Update society with new token
            $society->update([
                'auth_token' => $token,
                'last_login' => now()
            ]);

            // Return successful login response
            return response()->json([
                'name' => $society->name,
                'born_date' => $society->born_date,
                'gender' => $society->gender,
                'address' => $society->address,
                'token' => $token,
                'regional' => [
                    'id' => $society->regional ? $society->regional->id : null,
                    'province' => $society->regional ? $society->regional->province : null,
                    'district' => $society->regional ? $society->regional->district : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Logout as society
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Get token from request parameter
        $token = $request->input('token') ?? $request->header('Authorization');

        // Remove 'Bearer ' prefix if present
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        if (!$token) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        try {
            // Find society by token
            $society = Society::where('auth_token', $token)->first();

            if (!$society) {
                return response()->json([
                    'message' => 'Invalid token'
                ], 401);
            }

            // Clear the token
            $society->update([
                'auth_token' => null,
                'last_logout' => now()
            ]);

            return response()->json([
                'message' => 'Logout success'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get authenticated society information
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $token = $request->input('token') ?? $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        if (!$token) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        $society = Society::with('regional')->where('auth_token', $token)->first();

        if (!$society) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        return response()->json([
            'name' => $society->name,
            'born_date' => $society->born_date,
            'gender' => $society->gender,
            'address' => $society->address,
            'token' => $token,
            'regional' => [
                'id' => $society->regional ? $society->regional->id : null,
                'province' => $society->regional ? $society->regional->province : null,
                'district' => $society->regional ? $society->regional->district : null,
            ]
        ], 200);
    }
}
