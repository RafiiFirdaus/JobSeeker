<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\JobVacancy;
use App\Models\JobApplySociety;
use App\Models\JobApplyPosition;
use App\Models\AvailablePosition;
use App\Models\Validation;

class JobApplicationController extends Controller
{
    /**
     * Show job application form
     */
    public function create($vacancyId)
    {
        // Check session-based authentication
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Get society ID from session
        $societyId = Session::get('user_id', 1);

        // Check if society validation data has been accepted
        $validationAccepted = Validation::where('society_id', $societyId)
            ->where('status', 'accepted')
            ->exists();

        if (!$validationAccepted) {
            return redirect()->route('job-vacancies.index')
                ->with('error', 'Your data validation must be accepted by validator before applying for jobs');
        }

        // Check if already applied
        $alreadyApplied = JobApplySociety::where('society_id', $societyId)
            ->where('job_vacancy_id', $vacancyId)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->route('job-vacancies.show', $vacancyId)
                ->with('error', 'You have already applied for this job');
        }

        $jobVacancy = JobVacancy::with(['jobCategory', 'availablePositions'])
            ->findOrFail($vacancyId);

        return view('job-applications.create', compact('jobVacancy'));
    }

    /**
     * Store job application
     */
    public function store(Request $request)
    {
        // Check session-based authentication
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Get society ID from session
        $societyId = Session::get('user_id', 1);

        // Enhanced validation with proper field names
        $validator = Validator::make($request->all(), [
            'job_vacancy_id' => 'required|integer|exists:job_vacancies,id',
            'vacancy_id' => 'sometimes|integer|exists:job_vacancies,id', // API compatibility
            'selected_positions' => 'sometimes|array|min:1',
            'selected_positions.*' => 'integer|exists:available_positions,id',
            'positions' => 'sometimes|array|min:1', // API compatibility
            'positions.*' => 'integer|exists:available_positions,id',
            'notes' => 'required|string|max:1000'
        ], [
            'notes.required' => 'Please provide notes for your application',
            'selected_positions.required' => 'Please select at least one position',
            'selected_positions.min' => 'Please select at least one position',
            'positions.required' => 'Please select at least one position',
            'positions.min' => 'Please select at least one position'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get vacancy ID and positions (support both web and API field names)
        $vacancyId = $request->input('job_vacancy_id') ?: $request->input('vacancy_id');
        $positions = $request->input('selected_positions') ?: $request->input('positions');
        $notes = $request->input('notes');

        // Check validation status
        $validationAccepted = Validation::where('society_id', $societyId)
            ->where('status', 'accepted')
            ->exists();

        if (!$validationAccepted) {
            return redirect()->back()
                ->with('error', 'Your data validation must be accepted before applying')
                ->withInput();
        }

        // Check if already applied
        $alreadyApplied = JobApplySociety::where('society_id', $societyId)
            ->where('job_vacancy_id', $vacancyId)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->back()
                ->with('error', 'You can only apply once for each job')
                ->withInput();
        }

        // Verify positions belong to vacancy
        $validPositions = AvailablePosition::where('job_vacancy_id', $vacancyId)
            ->whereIn('id', $positions)
            ->pluck('id')
            ->toArray();

        if (count($validPositions) !== count($positions)) {
            return redirect()->back()
                ->with('error', 'Invalid positions selected')
                ->withInput();
        }

        try {
            // Create job application
            $jobApplication = JobApplySociety::create([
                'society_id' => $societyId,
                'job_vacancy_id' => $vacancyId,
                'notes' => $notes,
                'date' => now()
            ]);

            // Create position applications
            foreach ($positions as $positionId) {
                JobApplyPosition::create([
                    'society_id' => $societyId,
                    'job_vacancy_id' => $vacancyId,
                    'position_id' => $positionId,
                    'job_apply_societies_id' => $jobApplication->id,
                    'status' => 'pending',
                    'date' => now()
                ]);
            }

            return redirect()->route('job-applications.index')
                ->with('success', 'Job application submitted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit application. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show all user's job applications
     */
    public function index()
    {
        // Check session-based authentication
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Get society ID from session
        $societyId = Session::get('user_id', 1);

        $jobApplications = JobApplySociety::where('society_id', $societyId)
            ->with([
                'jobVacancy.jobCategory',
                'jobApplyPositions.availablePosition'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('job-applications.index', compact('jobApplications'));
    }

    /**
     * Show job application details
     */
    public function show($id)
    {
        // Check session-based authentication
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Get society ID from session
        $societyId = Session::get('user_id', 1);

        $jobApplication = JobApplySociety::where('society_id', $societyId)
            ->where('id', $id)
            ->with([
                'jobVacancy.jobCategory',
                'jobApplyPositions.availablePosition'
            ])
            ->firstOrFail();

        return view('job-applications.show', compact('jobApplication'));
    }

    /**
     * AJAX endpoint for getting job applications (session-based)
     */
    public function ajaxIndex(Request $request)
    {
        if (!Session::has('user_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Get logged-in user
        $societyId = Session::get('user_id', 1);

        // Build query
        $query = JobApplySociety::with([
                'jobVacancy.jobCategory',
                'jobApplyPositions.availablePosition'
            ])
            ->where('society_id', $societyId);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->whereHas('jobApplyPositions', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('jobVacancy', function($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Get page and limit
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);

        // Get total count
        $total = $query->count();

        // Get paginated results
        $applications = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        // Format response
        return response()->json([
            'success' => true,
            'data' => [
                'data' => $applications,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_items' => $total,
                    'per_page' => $limit
                ]
            ]
        ]);
    }
}
