<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Society;
use App\Models\JobApplySociety;
use App\Models\Validation;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Session::has('user_logged_in')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (Session::has('user_logged_in')) {
            return redirect()->route('dashboard');
        }

        // Validation
        $request->validate([
            'id_card_number' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // Find society by ID card number
            $society = Society::with('regional')->where('id_card_number', $request->id_card_number)->first();

            // Check if society exists and password is correct
            if (!$society || !Hash::check($request->password, $society->password)) {
                return redirect()->back()
                    ->withErrors(['login' => 'ID Card Number or Password incorrect'])
                    ->withInput($request->only('id_card_number'));
            }

            // Generate authentication token
            $token = md5($society->id_card_number . time());

            // Update society with new token
            $society->update([
                'auth_token' => $token,
                'last_login' => now()
            ]);

            // Store session data
            Session::put('user_logged_in', true);
            Session::put('user_id', $society->id);
            Session::put('user_name', $society->name);
            Session::put('user_id_card', $society->id_card_number);
            Session::put('user_token', $token);

            return redirect()->route('dashboard')->with('success', 'Login successful!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['login' => 'Login failed. Please try again.'])
                ->withInput($request->only('id_card_number'));
        }
    }

    public function showRegisterForm()
    {
        if (Session::has('user_logged_in')) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (Session::has('user_logged_in')) {
            return redirect()->route('dashboard');
        }

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'id_card_number' => 'required|string|max:20',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // In a real application, you would save to database
        // For demo purposes, we'll just redirect to login

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    public function logout(Request $request)
    {
        try {
            // Clear token from database if user is logged in
            if (Session::has('user_id') && Session::has('user_token')) {
                $society = Society::find(Session::get('user_id'));
                if ($society && $society->auth_token === Session::get('user_token')) {
                    $society->update([
                        'auth_token' => null,
                        'last_logout' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Continue with logout even if token clearing fails
        }

        // Clear all session data
        Session::forget('user_logged_in');
        Session::forget('user_id');
        Session::forget('user_name');
        Session::forget('user_id_card');
        Session::forget('user_token');

        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }

    public function ajaxUserStatus(Request $request)
    {
        try {
            $vacancyId = $request->get('vacancy_id');
            \Log::info('User status request received', ['vacancy_id' => $vacancyId]);

            // Check if user is logged in
            if (!Session::has('user_logged_in') || !Session::has('user_id')) {
                \Log::info('User not logged in');
                return response()->json([
                    'success' => true,
                    'data' => [
                        'isLoggedIn' => false,
                        'hasApplied' => false,
                        'validationAccepted' => false
                    ]
                ]);
            }

            $userId = Session::get('user_id');
            \Log::info('User logged in', ['user_id' => $userId]);

            $hasApplied = false;
            $validationAccepted = false;

            // Check if user has applied for specific vacancy
            if ($vacancyId) {
                $hasApplied = JobApplySociety::where('society_id', $userId)
                    ->where('job_vacancy_id', $vacancyId)
                    ->exists();
                \Log::info('Application check', ['has_applied' => $hasApplied]);
            }

            // Check if user has accepted validation
            $validation = Validation::where('society_id', $userId)
                ->where('status', 'accepted')
                ->first();

            $validationAccepted = $validation !== null;
            \Log::info('Validation check', ['validation_accepted' => $validationAccepted]);

            return response()->json([
                'success' => true,
                'data' => [
                    'isLoggedIn' => true,
                    'hasApplied' => $hasApplied,
                    'validationAccepted' => $validationAccepted,
                    'userId' => $userId,
                    'userName' => Session::get('user_name')
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('User status error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user status: ' . $e->getMessage()
            ], 500);
        }
    }
}
