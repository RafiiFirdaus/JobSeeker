<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['username' => 'admin', 'password' => Hash::make('admin123')],
            ['username' => 'validator1', 'password' => Hash::make('121212')],
            ['username' => 'validator2', 'password' => Hash::make('121212')],
            ['username' => 'validator3', 'password' => Hash::make('121212')],
            ['username' => 'officer1', 'password' => Hash::make('121212')],
            ['username' => 'officer2', 'password' => Hash::make('121212')],
            ['username' => 'officer3', 'password' => Hash::make('121212')],
        ];

        foreach ($users as $user) {
            DB::table('admin_users')->insert([
                'username' => $user['username'],
                'password' => $user['password'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
