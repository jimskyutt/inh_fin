<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $barangays = [
            ['brgy_name' => 'Agpipili', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Alcantara', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Almeana', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Anabo', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Bankal', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Buenavista', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Cabantohan', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Capiñahan', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Dalipe','created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Dapdapan', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Gerongan', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Imbaulan', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Layogbato', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Marapal', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Milan', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Nagsulang', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Nasapahan', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Omio', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Pacuan', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Poblacion NW Zone', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Poblacion SE Zone', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'Pontoc', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'San Antonio', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'San Diego', 'created_at' => $now, 'updated_at' => $now],
            ['brgy_name' => 'San Jose Moto', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('barangays')->insert($barangays);
    }
}