<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobCategory;

class JobCategoryController extends Controller
{
    /**
     * Get all job categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $jobCategories = JobCategory::orderBy('name', 'asc')->get();

            $formattedCategories = $jobCategories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description ?? null
                ];
            });

            return response()->json([
                'job_categories' => $formattedCategories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve job categories'
            ], 500);
        }
    }
}
