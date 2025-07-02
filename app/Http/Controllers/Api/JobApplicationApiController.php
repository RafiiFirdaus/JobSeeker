<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Society;
use App\Models\JobVacancy;
use App\Models\JobApplySociety;
use App\Models\JobApplyPosition;
use App\Models\AvailablePosition;
use App\Models\Validation;

class JobApplicationApiController extends Controller
{
    /**
     * Submit job application
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

        // A4c - Check if society validation data has been accepted
        $validationAccepted = Validation::where('society_id', $society->id)
            ->where('status', 'accepted')
            ->exists();

        if (!$validationAccepted) {
            return response()->json([
                'message' => 'Your data validator must be accepted by validator before'
            ], 401);
        }

        // A4d - Validate request fields
        $validator = Validator::make($request->all(), [
            'vacancy_id' => 'required|integer|exists:job_vacancies,id',
            'positions' => 'required|array|min:1',
            'positions.*' => 'integer|exists:available_positions,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 401);
        }

        $vacancyId = $request->input('vacancy_id');
        $positions = $request->input('positions');
        $notes = $request->input('notes', '');

        try {
            // A4e - Check if society has already applied for this job vacancy
            $existingApplication = JobApplySociety::where('society_id', $society->id)
                ->where('job_vacancy_id', $vacancyId)
                ->exists();

            if ($existingApplication) {
                return response()->json([
                    'message' => 'Application for a job can only be once'
                ], 401);
            }

            // Verify all positions belong to the specified vacancy
            $validPositions = AvailablePosition::where('job_vacancy_id', $vacancyId)
                ->whereIn('id', $positions)
                ->pluck('id')
                ->toArray();

            if (count($validPositions) !== count($positions)) {
                return response()->json([
                    'message' => 'Invalid field',
                    'errors' => [
                        'positions' => ['Some positions do not belong to the specified vacancy.']
                    ]
                ], 401);
            }

            // Create job application
            $jobApplication = JobApplySociety::create([
                'society_id' => $society->id,
                'job_vacancy_id' => $vacancyId,
                'notes' => $notes,
                'date' => now()
            ]);

            // Create position applications
            foreach ($positions as $positionId) {
                JobApplyPosition::create([
                    'society_id' => $society->id,
                    'job_vacancy_id' => $vacancyId,
                    'position_id' => $positionId,
                    'job_apply_societies_id' => $jobApplication->id,
                    'status' => 'pending',
                    'date' => now()
                ]);
            }

            return response()->json([
                'message' => 'Applying for job successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit job application'
            ], 500);
        }
    }

    /**
     * Get all society job applications
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
            // Get all job applications for this society
            $jobApplications = JobApplySociety::where('society_id', $society->id)
                ->with([
                    'jobVacancy.jobCategory',
                    'jobApplyPositions.availablePosition'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Format response
            $vacancies = $jobApplications->map(function ($application) {
                $positions = $application->jobApplyPositions->map(function ($positionApp) use ($application) {
                    return [
                        'position' => $positionApp->availablePosition ? $positionApp->availablePosition->position : 'Unknown',
                        'apply_status' => $positionApp->status ?? 'pending',
                        'notes' => $application->notes ?? ''
                    ];
                })->toArray();

                return [
                    'id' => $application->jobVacancy->id,
                    'category' => [
                        'id' => $application->jobVacancy->jobCategory ? $application->jobVacancy->jobCategory->id : null,
                        'job_category' => $application->jobVacancy->jobCategory ? $application->jobVacancy->jobCategory->name : null,
                    ],
                    'company' => $application->jobVacancy->company,
                    'address' => $application->jobVacancy->address,
                    'position' => $positions
                ];
            });

            return response()->json([
                'vacancies' => $vacancies
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve job applications'
            ], 500);
        }
    }
}
