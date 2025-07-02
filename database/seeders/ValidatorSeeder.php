<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValidatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $validators = [
            ['user_id' => 2, 'role' => 'validator', 'name' => 'Kamila Wibisono'],
            ['user_id' => 3, 'role' => 'validator', 'name' => 'Maya Kusmawati'],
            ['user_id' => 4, 'role' => 'validator', 'name' => 'Gaduh Prasetyo'],
            ['user_id' => 5, 'role' => 'officer', 'name' => 'Indra Usamah'],
            ['user_id' => 6, 'role' => 'officer', 'name' => 'Kalim Yulianti'],
            ['user_id' => 7, 'role' => 'officer', 'name' => 'Eva Mandasari'],
        ];

        foreach ($validators as $validator) {
            DB::table('validators')->insert([
                'user_id' => $validator['user_id'],
                'role' => $validator['role'],
                'name' => $validator['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
