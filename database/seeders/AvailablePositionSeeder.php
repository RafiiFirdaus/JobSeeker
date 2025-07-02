<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AvailablePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            // PT. Maju Mundur Sejahtera
            ['job_vacancy_id' => 1, 'position' => 'Desain Grafis', 'capacity' => 3, 'apply_capacity' => 12],
            ['job_vacancy_id' => 1, 'position' => 'Programmer', 'capacity' => 1, 'apply_capacity' => 8],
            ['job_vacancy_id' => 1, 'position' => 'Manager', 'capacity' => 1, 'apply_capacity' => 22],

            // PT. Tech Innovation
            ['job_vacancy_id' => 2, 'position' => 'Full Stack Developer', 'capacity' => 2, 'apply_capacity' => 15],
            ['job_vacancy_id' => 2, 'position' => 'UI/UX Designer', 'capacity' => 1, 'apply_capacity' => 10],

            // PT. Digital Solutions
            ['job_vacancy_id' => 3, 'position' => 'Backend Developer', 'capacity' => 2, 'apply_capacity' => 18],
            ['job_vacancy_id' => 3, 'position' => 'DevOps Engineer', 'capacity' => 1, 'apply_capacity' => 12],
            ['job_vacancy_id' => 3, 'position' => 'QA Tester', 'capacity' => 2, 'apply_capacity' => 20],

            // PT. Creative Agency
            ['job_vacancy_id' => 4, 'position' => 'Graphic Designer', 'capacity' => 3, 'apply_capacity' => 25],
            ['job_vacancy_id' => 4, 'position' => 'Content Writer', 'capacity' => 2, 'apply_capacity' => 15],
            ['job_vacancy_id' => 4, 'position' => 'Marketing', 'capacity' => 1, 'apply_capacity' => 10],

            // PT. Manufacturing Corp
            ['job_vacancy_id' => 5, 'position' => 'Production Manager', 'capacity' => 1, 'apply_capacity' => 8],
            ['job_vacancy_id' => 5, 'position' => 'Quality Control', 'capacity' => 2, 'apply_capacity' => 16],
            ['job_vacancy_id' => 5, 'position' => 'Operator', 'capacity' => 5, 'apply_capacity' => 40],
        ];

        foreach ($positions as $position) {
            DB::table('available_positions')->insert([
                'job_vacancy_id' => $position['job_vacancy_id'],
                'position' => $position['position'],
                'capacity' => $position['capacity'],
                'apply_capacity' => $position['apply_capacity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
