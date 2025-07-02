<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Validation;
use App\Models\JobApplySociety;
use App\Models\JobVacancy;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Session::has('user_logged_in')) {
            return redirect()->route('login');
        }

        // Get data validations for current user (using demo society_id = 1)
        $dataValidations = Validation::with(['jobCategory', 'validator'])
            ->where('society_id', 1)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get job applications for current user (using demo society_id = 1)
        $jobApplications = JobApplySociety::with(['jobVacancy', 'jobApplyPositions.availablePosition'])
            ->where('society_id', 1)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('dataValidations', 'jobApplications'));
    }
}
