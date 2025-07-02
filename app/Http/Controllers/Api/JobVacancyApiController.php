<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobVacancy;
use App\Models\Society;
use App\Models\JobApplySociety;

class JobVacancyApiController extends Controller
{
    /**
     * Get all job vacancies
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
            // Get all job vacancies with related data
            $jobVacancies = JobVacancy::with(['jobCategory', 'availablePositions'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get user's job applications to mark applied vacancies
            $appliedVacancyIds = JobApplySociety::where('society_id', $society->id)
                ->pluck('job_vacancy_id')
                ->toArray();

            // Format response
            $vacancies = $jobVacancies->map(function ($vacancy) use ($appliedVacancyIds) {
                return [
                    'id' => $vacancy->id,
                    'category' => [
                        'id' => $vacancy->jobCategory ? $vacancy->jobCategory->id : null,
                        'job_category' => $vacancy->jobCategory ? $vacancy->jobCategory->name : null,
                    ],
                    'company' => $vacancy->company,
                    'address' => $vacancy->address,
                    'description' => $vacancy->description,
                    'has_applied' => in_array($vacancy->id, $appliedVacancyIds),
                    'available_position' => $vacancy->availablePositions->map(function ($position) {
                        // Get application count for this position
                        $applyCount = \App\Models\JobApplyPosition::where('position_id', $position->id)->count();

                        return [
                            'id' => $position->id,
                            'position' => $position->position,
                            'capacity' => $position->capacity,
                            'apply_capacity' => $position->apply_capacity,
                            'apply_count' => $applyCount,
                        ];
                    })->toArray()
                ];
            });

            return response()->json([
                'vacancies' => $vacancies
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve job vacancies'
            ], 500);
        }
    }

    /**
     * Get job vacancy detail by ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
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
            // Get job vacancy with related data
            $jobVacancy = JobVacancy::with(['jobCategory', 'availablePositions'])
                ->findOrFail($id);

            // Check if user has applied for this vacancy
            $hasApplied = JobApplySociety::where('society_id', $society->id)
                ->where('job_vacancy_id', $jobVacancy->id)
                ->exists();

            // Format response
            $vacancy = [
                'id' => $jobVacancy->id,
                'category' => [
                    'id' => $jobVacancy->jobCategory ? $jobVacancy->jobCategory->id : null,
                    'job_category' => $jobVacancy->jobCategory ? $jobVacancy->jobCategory->name : null,
                ],
                'company' => $jobVacancy->company,
                'address' => $jobVacancy->address,
                'description' => $jobVacancy->description,
                'has_applied' => $hasApplied,
                'created_at' => $jobVacancy->created_at->format('Y-m-d H:i:s'),
                'available_position' => $jobVacancy->availablePositions->map(function ($position) {
                    // Get application count for this position
                    $applyCount = \App\Models\JobApplyPosition::where('position_id', $position->id)->count();

                    return [
                        'id' => $position->id,
                        'position' => $position->position,
                        'capacity' => $position->capacity,
                        'apply_capacity' => $position->apply_capacity,
                        'apply_count' => $applyCount,
                        'is_full' => $applyCount >= $position->apply_capacity,
                    ];
                })->toArray()
            ];

            return response()->json([
                'vacancy' => $vacancy
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Job vacancy not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve job vacancy details'
            ], 500);
        }
    }

    /**
     * Get job vacancies by category
     *
     * @param Request $request
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCategory(Request $request, $categoryId)
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
            // Build query with category filter
            $jobVacancies = JobVacancy::with(['jobCategory', 'availablePositions'])
                ->where('job_category_id', $categoryId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get user's job applications
            $appliedVacancyIds = JobApplySociety::where('society_id', $society->id)
                ->pluck('job_vacancy_id')
                ->toArray();

            // Format response
            $vacancies = $jobVacancies->map(function ($vacancy) use ($appliedVacancyIds) {
                return [
                    'id' => $vacancy->id,
                    'category' => [
                        'id' => $vacancy->jobCategory ? $vacancy->jobCategory->id : null,
                        'job_category' => $vacancy->jobCategory ? $vacancy->jobCategory->name : null,
                    ],
                    'company' => $vacancy->company,
                    'address' => $vacancy->address,
                    'description' => $vacancy->description,
                    'has_applied' => in_array($vacancy->id, $appliedVacancyIds),
                    'available_position' => $vacancy->availablePositions->map(function ($position) {
                        $applyCount = \App\Models\JobApplyPosition::where('position_id', $position->id)->count();

                        return [
                            'id' => $position->id,
                            'position' => $position->position,
                            'capacity' => $position->capacity,
                            'apply_capacity' => $position->apply_capacity,
                            'apply_count' => $applyCount,
                        ];
                    })->toArray()
                ];
            });

            return response()->json([
                'vacancies' => $vacancies,
                'total_count' => $vacancies->count(),
                'applied_count' => $jobVacancies->whereIn('id', $appliedVacancyIds)->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve job vacancies by category'
            ], 500);
        }
    }
}
