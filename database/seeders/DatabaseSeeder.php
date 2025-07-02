<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RegionalSeeder::class,
            JobCategorySeeder::class,
            SocietySeeder::class,
            AdminUserSeeder::class,
            ValidatorSeeder::class,
            JobVacancySeeder::class,
            AvailablePositionSeeder::class,
            ValidationSeeder::class,
            JobApplicationSeeder::class,
        ]);
    }
}
