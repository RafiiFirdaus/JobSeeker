<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Validation;
use App\Models\JobCategory;
use App\Models\Society;

class DataValidationController extends Controller
{
    public function create()
    {
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login');
        }

        $jobCategories = JobCategory::all();
        return view('data-validation.create', compact('jobCategories'));
    }

    public function store(Request $request)
    {
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login');
        }

        // Validation
        $request->validate([
            'job_category' => 'required|string|exists:job_categories,name',
            'job_positions' => 'required|string',
            'has_experience' => 'required|string|in:yes,no',
            'work_experiences' => 'nullable|string',
            'reason_accepted' => 'required|string',
        ]);

        try {
            // Get logged-in user
            $userId = Session::get('user_id', 1); // Default to 1 if not set

            // Find job category
            $jobCategory = JobCategory::where('name', $request->job_category)->first();

            // Check if user already has a pending validation
            $existingValidation = Validation::where('society_id', $userId)
                ->where('status', 'pending')
                ->first();

            if ($existingValidation) {
                return redirect()->back()->withErrors(['error' => 'You already have a pending validation request.'])->withInput();
            }

            // Create validation request
            Validation::create([
                'society_id' => $userId,
                'job_category_id' => $jobCategory->id,
                'position' => $request->job_positions,
                'has_experience' => $request->has_experience === 'yes',
                'work_experience' => $request->work_experiences,
                'reason' => $request->reason_accepted,
                'status' => 'pending',
            ]);

            return redirect()->route('dashboard')->with('success', 'Data validation request submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to submit validation request. Please try again.'])->withInput();
        }
    }

    /**
     * Show validation progress and results
     */
    public function progress()
    {
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login');
        }

        // Get logged-in user
        $userId = Session::get('user_id', 1); // Default to 1 if not set

        // Get all validations for this user
        $validations = Validation::with(['jobCategory', 'validator'])
            ->where('society_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('data-validation.progress', compact('validations'));
    }

    /**
     * Show validation results
     */
    public function results()
    {
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login');
        }

        // Get logged-in user
        $userId = Session::get('user_id', 1); // Default to 1 if not set

        // Get accepted and rejected validations
        $validations = Validation::with(['jobCategory', 'validator'])
            ->where('society_id', $userId)
            ->whereIn('status', ['accepted', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('data-validation.results', compact('validations'));
    }

    /**
     * AJAX endpoint for getting validations (session-based)
     */
    public function ajaxIndex(Request $request)
    {
        if (!Session::has('user_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Get logged-in user
        $userId = Session::get('user_id', 1);

        // Build query
        $query = Validation::with(['jobCategory', 'validator'])
            ->where('society_id', $userId);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('job_category_id', $request->category_id);
        }

        // Get page and limit
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);

        // Get total count
        $total = $query->count();

        // Get paginated results
        $validations = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        // Format response
        return response()->json([
            'success' => true,
            'data' => [
                'data' => $validations,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_items' => $total,
                    'per_page' => $limit
                ]
            ]
        ]);
    }

    /**
     * AJAX endpoint for getting job categories
     */
    public function ajaxJobCategories()
    {
        try {
            $categories = JobCategory::select('id', 'job_category as name')->orderBy('job_category')->get();

            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch job categories'
            ], 500);
        }
    }

    /**
     * AJAX endpoint for storing validation (session-based)
     */
    public function ajaxStore(Request $request)
    {
        if (!Session::has('user_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Validation
        $validator = \Validator::make($request->all(), [
            'job_category_id' => 'required|exists:job_categories,id',
            'job_position' => 'required|string|max:1000',
            'work_experience' => 'required|string',
            'reason_accepted' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get logged-in user
            $userId = Session::get('user_id', 1);

            // Check if user already has a pending validation
            $existingValidation = Validation::where('society_id', $userId)
                ->where('status', 'pending')
                ->first();

            if ($existingValidation) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a pending validation request. Please wait for it to be processed.'
                ], 422);
            }

            // Create validation
            $validation = Validation::create([
                'society_id' => $userId,
                'job_category_id' => $request->job_category_id,
                'position' => $request->job_position,
                'work_experience' => $request->work_experience,
                'reason_accepted' => $request->reason_accepted,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Validation request submitted successfully! Please wait for validator review.',
                'data' => $validation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit validation request. Please try again.'
            ], 500);
        }
    }
}
