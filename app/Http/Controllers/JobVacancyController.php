<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\JobVacancy;
use App\Models\JobCategory;
use App\Models\AvailablePosition;
use App\Models\JobApplySociety;
use App\Models\JobApplyPosition;

class JobVacancyController extends Controller
{
    public function index()
    {
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login');
        }

        $userId = Session::get('user_id', 1);

        // Get job vacancies from database with related data
        $jobVacancies = JobVacancy::with(['jobCategory', 'availablePositions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get user's applied vacancy IDs
        $appliedVacancyIds = JobApplySociety::where('society_id', $userId)
            ->pluck('job_vacancy_id')
            ->toArray();

        // Add applied status to each vacancy
        foreach ($jobVacancies as $vacancy) {
            $vacancy->has_applied = in_array($vacancy->id, $appliedVacancyIds);

            // Add application counts for each position
            foreach ($vacancy->availablePositions as $position) {
                $position->applications_count = JobApplyPosition::where('available_position_id', $position->id)->count();
                $position->max_applications = $position->max_applications ?? ($position->capacity * 10);
            }
        }

        return view('job-vacancies.index', compact('jobVacancies'));
    }

    public function show($id)
    {
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login');
        }

        // Get job vacancy with related data
        $jobVacancy = JobVacancy::with(['jobCategory', 'availablePositions'])
            ->findOrFail($id);

        return view('job-vacancies.show', compact('jobVacancy'));
    }

    /**
     * AJAX endpoint for getting job vacancies (session-based)
     */
    public function ajaxIndex(Request $request)
    {
        if (!Session::has('user_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Get logged-in user
        $userId = Session::get('user_id', 1);

        // Build query
        $query = JobVacancy::with(['jobCategory', 'availablePositions']);

        // Apply filters
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('jobCategory', function($categoryQuery) use ($search) {
                      $categoryQuery->where('job_category', 'like', "%{$search}%");
                  });
            });
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
        $vacancies = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        // Get user's applied vacancy IDs
        $appliedVacancyIds = JobApplySociety::where('society_id', $userId)
            ->pluck('job_vacancy_id')
            ->toArray();

        // Add applied status and application counts
        foreach ($vacancies as $vacancy) {
            $vacancy->has_applied = in_array($vacancy->id, $appliedVacancyIds);

            // Add application counts for each position
            foreach ($vacancy->availablePositions as $position) {
                $position->applications_count = JobApplyPosition::where('position_id', $position->id)->count();
                $position->max_applications = $position->capacity * 10; // Allow 10x capacity for applications
            }
        }

        // Format response
        return response()->json([
            'success' => true,
            'data' => [
                'data' => $vacancies,
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
     * AJAX endpoint for getting single job vacancy (session-based)
     */
    public function ajaxShow(Request $request, $id)
    {
        if (!Session::has('user_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $userId = Session::get('user_id', 1);

            // Get job vacancy with related data
            $vacancy = JobVacancy::with(['jobCategory', 'availablePositions'])
                ->findOrFail($id);

            // Check if user has applied
            $hasApplied = JobApplySociety::where('society_id', $userId)
                ->where('job_vacancy_id', $id)
                ->exists();

            $vacancy->has_applied = $hasApplied;

            // Add application counts for each position
            foreach ($vacancy->availablePositions as $position) {
                $position->applications_count = JobApplyPosition::where('position_id', $position->id)->count();
                $position->max_applications = $position->capacity * 10;
            }

            return response()->json([
                'success' => true,
                'data' => $vacancy
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job vacancy not found'
            ], 404);
        }
    }
}
