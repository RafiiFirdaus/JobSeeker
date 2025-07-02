<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $validations = [
            [
                'job_category_id' => 1, // Computing and ICT
                'society_id' => 1, // Omar Gunawan
                'validator_id' => null,
                'status' => 'pending',
                'work_experience' => 'Fresh graduate with some internship experience',
                'job_position' => 'Web Developer, Frontend Developer',
                'reason_accepted' => 'I am passionate about web development and eager to learn new technologies',
                'validator_notes' => null,
            ],
            [
                'job_category_id' => 1, // Computing and ICT
                'society_id' => 2, // Nilam Sinaga
                'validator_id' => 1, // Kamila Wibisono
                'status' => 'accepted',
                'work_experience' => '2 years experience as a programmer',
                'job_position' => 'Programmer, Software Developer',
                'reason_accepted' => 'I can work hard and have proven experience in software development',
                'validator_notes' => 'Good candidate with solid programming skills',
            ],
            [
                'job_category_id' => 4, // Design, arts and crafts
                'society_id' => 3, // Rosman Lailasari
                'validator_id' => 2, // Maya Kusmawati
                'status' => 'accepted',
                'work_experience' => '3 years experience in graphic design',
                'job_position' => 'Graphic Designer, UI Designer',
                'reason_accepted' => 'Creative and experienced in various design tools',
                'validator_notes' => 'Excellent portfolio and creative skills',
            ],
            [
                'job_category_id' => 1, // Computing and ICT
                'society_id' => 4, // Ifa Adriansyah
                'validator_id' => null,
                'status' => 'pending',
                'work_experience' => '1 year experience in web development',
                'job_position' => 'Full Stack Developer',
                'reason_accepted' => 'Ready to contribute and grow with the company',
                'validator_notes' => null,
            ],
        ];

        foreach ($validations as $validation) {
            DB::table('validations')->insert([
                'job_category_id' => $validation['job_category_id'],
                'society_id' => $validation['society_id'],
                'validator_id' => $validation['validator_id'],
                'status' => $validation['status'],
                'work_experience' => $validation['work_experience'],
                'job_position' => $validation['job_position'],
                'reason_accepted' => $validation['reason_accepted'],
                'validator_notes' => $validation['validator_notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
