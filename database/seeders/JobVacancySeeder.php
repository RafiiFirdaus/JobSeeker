<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobVacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobVacancies = [
            [
                'job_category_id' => 1, // Computing and ICT
                'company' => 'PT. Maju Mundur Sejahtera',
                'address' => 'Jln. HOS. Cjokroaminoto (Pasirkaliki) No. 900, DKI Jakarta',
                'description' => 'We are looking for talented individuals to join our growing company. We offer competitive salary, good work environment, and opportunities for career advancement.',
            ],
            [
                'job_category_id' => 1, // Computing and ICT
                'company' => 'PT. Tech Innovation',
                'address' => 'Jln. Sudirman No. 123, Jakarta Pusat',
                'description' => 'Join our innovative team of developers and designers. We create cutting-edge solutions for modern businesses.',
            ],
            [
                'job_category_id' => 1, // Computing and ICT
                'company' => 'PT. Digital Solutions',
                'address' => 'Jln. Gatot Subroto No. 456, Jakarta Selatan',
                'description' => 'Leading digital agency looking for passionate developers and IT professionals to join our team.',
            ],
            [
                'job_category_id' => 4, // Design, arts and crafts
                'company' => 'PT. Creative Agency',
                'address' => 'Jln. Thamrin No. 789, Jakarta Pusat',
                'description' => 'Creative agency specializing in branding, graphic design, and digital marketing solutions.',
            ],
            [
                'job_category_id' => 8, // Manufacturing and engineering
                'company' => 'PT. Manufacturing Corp',
                'address' => 'Jln. Industri No. 101, Bekasi',
                'description' => 'Large manufacturing company with state-of-the-art facilities looking for skilled workers.',
            ],
        ];

        foreach ($jobVacancies as $vacancy) {
            DB::table('job_vacancies')->insert([
                'job_category_id' => $vacancy['job_category_id'],
                'company' => $vacancy['company'],
                'address' => $vacancy['address'],
                'description' => $vacancy['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
