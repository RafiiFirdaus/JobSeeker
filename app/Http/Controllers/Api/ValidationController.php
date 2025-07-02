<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Validation;
use App\Models\JobCategory;
use App\Models\Society;

class ValidationController extends Controller
{
    /**
     * Request data validation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Get token from request parameter
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized user'
            ], 401);
        }

        // Find society by token
        $society = Society::where('auth_token', $token)->first();

        if (!$society) {
            return response()->json([
                'message' => 'Unauthorized user'
            ], 401);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'work_experience' => 'nullable|string|max:1000',
            'job_category' => 'required|integer|exists:job_categories,id',
            'job_position' => 'required|string|max:255',
            'reason_accepted' => 'required|string|max:1000'
        ], [
            'job_category.required' => 'Job category is required',
            'job_category.exists' => 'Invalid job category',
            'job_position.required' => 'Job position is required',
            'reason_accepted.required' => 'Reason for acceptance is required'
        ]);

        if ($validator->fails()) {
            // Check if any required fields are empty
            $errors = $validator->errors();
            if ($errors->has('job_position') || $errors->has('reason_accepted') || $errors->has('job_category')) {
                return response()->json([
                    'message' => 'Data ada yang kosong',
                    'errors' => $errors
                ], 422);
            }

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        }

        try {
            // Check if society already has a pending validation
            $existingValidation = Validation::where('society_id', $society->id)
                ->where('status', 'pending')
                ->first();

            if ($existingValidation) {
                return response()->json([
                    'message' => 'You already have a pending validation request'
                ], 409);
            }

            // Create validation request
            $validation = Validation::create([
                'society_id' => $society->id,
                'job_category_id' => $request->job_category,
                'position' => $request->job_position,
                'has_experience' => !empty($request->work_experience),
                'work_experience' => $request->work_experience,
                'reason' => $request->reason_accepted,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Request data validation sent successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit validation request'
            ], 500);
        }
    }

    /**
     * Get society data validation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get token from request parameter
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized user'
            ], 401);
        }

        // Find society by token
        $society = Society::where('auth_token', $token)->first();

        if (!$society) {
            return response()->json([
                'message' => 'Unauthorized user'
            ], 401);
        }

        try {
            // Get the latest validation for this society
            $validation = Validation::with(['jobCategory', 'validator'])
                ->where('society_id', $society->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$validation) {
                return response()->json([
                    'validation' => null
                ], 200);
            }

            // Format response
            $response = [
                'validation' => [
                    'id' => $validation->id,
                    'status' => $validation->status,
                    'work_experience' => $validation->work_experience,
                    'job_category_id' => $validation->job_category_id,
                    'job_position' => $validation->position,
                    'reason_accepted' => $validation->reason,
                    'validator_notes' => $validation->validator_notes,
                    'validator' => null
                ]
            ];

            // Add validator information if exists
            if ($validation->validator) {
                $response['validation']['validator'] = [
                    'id' => $validation->validator->id,
                    'name' => $validation->validator->name,
                    'email' => $validation->validator->email ?? null,
                ];
            }

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve validation data'
            ], 500);
        }
    }

    /**
     * Get all validations for society (for progress tracking)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        // Get token from request parameter
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized user'
            ], 401);
        }

        // Find society by token
        $society = Society::where('auth_token', $token)->first();

        if (!$society) {
            return response()->json([
                'message' => 'Unauthorized user'
            ], 401);
        }

        try {
            // Get all validations for this society
            $validations = Validation::with(['jobCategory', 'validator'])
                ->where('society_id', $society->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedValidations = $validations->map(function ($validation) {
                return [
                    'id' => $validation->id,
                    'status' => $validation->status,
                    'work_experience' => $validation->work_experience,
                    'job_category_id' => $validation->job_category_id,
                    'job_category_name' => $validation->jobCategory ? $validation->jobCategory->name : null,
                    'job_position' => $validation->position,
                    'reason_accepted' => $validation->reason,
                    'validator_notes' => $validation->validator_notes,
                    'created_at' => $validation->created_at->format('Y-m-d H:i:s'),
                    'validator' => $validation->validator ? [
                        'id' => $validation->validator->id,
                        'name' => $validation->validator->name,
                        'email' => $validation->validator->email ?? null,
                    ] : null
                ];
            });

            return response()->json([
                'validations' => $formattedValidations
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve validation history'
            ], 500);
        }
    }
}
