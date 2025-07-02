<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regionals = [
            ['province' => 'DKI Jakarta', 'district' => 'Central Jakarta'],
            ['province' => 'DKI Jakarta', 'district' => 'South Jakarta'],
            ['province' => 'West Java', 'district' => 'Bandung'],
        ];

        foreach ($regionals as $regional) {
            DB::table('regionals')->insert([
                'province' => $regional['province'],
                'district' => $regional['district'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
