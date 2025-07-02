<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Job Apply Societies
        $jobApplySocieties = [
            [
                'notes' => 'I am very interested in this position and ready to contribute to the company',
                'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'society_id' => 2, // Nilam Sinaga
                'job_vacancy_id' => 1, // PT. Maju Mundur Sejahtera
            ],
            [
                'notes' => 'Ready to start immediately and bring fresh perspectives',
                'date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'society_id' => 3, // Rosman Lailasari
                'job_vacancy_id' => 4, // PT. Creative Agency
            ],
            [
                'notes' => 'Excited to work with innovative technologies',
                'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'society_id' => 4, // Ifa Adriansyah
                'job_vacancy_id' => 2, // PT. Tech Innovation
            ],
        ];

        foreach ($jobApplySocieties as $index => $application) {
            DB::table('job_apply_societies')->insert([
                'notes' => $application['notes'],
                'date' => $application['date'],
                'society_id' => $application['society_id'],
                'job_vacancy_id' => $application['job_vacancy_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Job Apply Positions
        $jobApplyPositions = [
            // Nilam Sinaga applies for Programmer at PT. Maju Mundur Sejahtera
            [
                'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'society_id' => 2,
                'job_vacancy_id' => 1,
                'position_id' => 2, // Programmer position
                'job_apply_societies_id' => 1,
                'status' => 'pending',
            ],
            // Rosman Lailasari applies for Graphic Designer at PT. Creative Agency
            [
                'date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'society_id' => 3,
                'job_vacancy_id' => 4,
                'position_id' => 9, // Graphic Designer position
                'job_apply_societies_id' => 2,
                'status' => 'accepted',
            ],
            // Ifa Adriansyah applies for Full Stack Developer at PT. Tech Innovation
            [
                'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'society_id' => 4,
                'job_vacancy_id' => 2,
                'position_id' => 4, // Full Stack Developer position
                'job_apply_societies_id' => 3,
                'status' => 'pending',
            ],
        ];

        foreach ($jobApplyPositions as $position) {
            DB::table('job_apply_positions')->insert([
                'date' => $position['date'],
                'society_id' => $position['society_id'],
                'job_vacancy_id' => $position['job_vacancy_id'],
                'position_id' => $position['position_id'],
                'job_apply_societies_id' => $position['job_apply_societies_id'],
                'status' => $position['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
