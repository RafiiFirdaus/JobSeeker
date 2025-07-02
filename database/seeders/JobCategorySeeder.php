<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Computing and ICT',
            'Construction and building',
            'Animals, land and environment',
            'Design, arts and crafts',
            'Education and training',
            'Healthcare and medicine',
            'Business and finance',
            'Manufacturing and engineering',
            'Transportation and logistics',
            'Hospitality and tourism',
        ];

        foreach ($categories as $category) {
            DB::table('job_categories')->insert([
                'job_category' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
