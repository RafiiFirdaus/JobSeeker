<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SocietySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $societies = [
            [
                'id_card_number' => '20210001',
                'password' => Hash::make('121212'),
                'name' => 'Omar Gunawan',
                'born_date' => '1990-04-18',
                'gender' => 'male',
                'address' => 'Jln. Baranang Siang No. 479, DKI Jakarta',
                'regional_id' => 1,
            ],
            [
                'id_card_number' => '20210002',
                'password' => Hash::make('121212'),
                'name' => 'Nilam Sinaga',
                'born_date' => '1998-09-11',
                'gender' => 'female',
                'address' => 'Gg. Sukajadi No. 26, DKI Jakarta',
                'regional_id' => 1,
            ],
            [
                'id_card_number' => '20210003',
                'password' => Hash::make('121212'),
                'name' => 'Rosman Lailasari',
                'born_date' => '1983-02-12',
                'gender' => 'male',
                'address' => 'Jln. Moch. Ramdan No. 242, DKI Jakarta',
                'regional_id' => 1,
            ],
            [
                'id_card_number' => '20210004',
                'password' => Hash::make('121212'),
                'name' => 'Ifa Adriansyah',
                'born_date' => '1993-05-17',
                'gender' => 'female',
                'address' => 'Gg. Setia Budi No. 215, DKI Jakarta',
                'regional_id' => 1,
            ],
            [
                'id_card_number' => '20210005',
                'password' => Hash::make('121212'),
                'name' => 'Sakura Susanti',
                'born_date' => '1973-11-05',
                'gender' => 'male',
                'address' => 'Kpg. B.Agam 1 No. 729, DKI Jakarta',
                'regional_id' => 1,
            ],
            [
                'id_card_number' => '20210016',
                'password' => Hash::make('121212'),
                'name' => 'Ina Nasyiah',
                'born_date' => '1971-05-21',
                'gender' => 'female',
                'address' => 'Ds. Suryo No. 100, DKI Jakarta',
                'regional_id' => 2,
            ],
            [
                'id_card_number' => '20210017',
                'password' => Hash::make('121212'),
                'name' => 'Jinawi Wastuti',
                'born_date' => '1994-06-18',
                'gender' => 'male',
                'address' => 'Ki. Sugiono No. 918, DKI Jakarta',
                'regional_id' => 2,
            ],
            [
                'id_card_number' => '20210031',
                'password' => Hash::make('121212'),
                'name' => 'Irwan Sinaga',
                'born_date' => '1976-10-06',
                'gender' => 'female',
                'address' => 'Dk. Basmol Raya No. 714, West Java',
                'regional_id' => 3,
            ],
            [
                'id_card_number' => '20210032',
                'password' => Hash::make('121212'),
                'name' => 'Lulut Lestari',
                'born_date' => '1981-03-31',
                'gender' => 'male',
                'address' => 'Ds. Cihampelas No. 933, West Java',
                'regional_id' => 3,
            ],
            [
                'id_card_number' => '20210033',
                'password' => Hash::make('121212'),
                'name' => 'Balijan Rahimah',
                'born_date' => '1972-04-25',
                'gender' => 'female',
                'address' => 'Ki. Ciwastra No. 539, West Java',
                'regional_id' => 3,
            ],
        ];

        foreach ($societies as $society) {
            DB::table('societies')->insert([
                'id_card_number' => $society['id_card_number'],
                'password' => $society['password'],
                'name' => $society['name'],
                'born_date' => $society['born_date'],
                'gender' => $society['gender'],
                'address' => $society['address'],
                'regional_id' => $society['regional_id'],
                'login_tokens' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
