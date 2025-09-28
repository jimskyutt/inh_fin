<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $services = [
            ['service_name' => 'Plumbing', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Electrical', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Carpentry', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Cleaning', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Painting', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Gardening', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Appliance Repair', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Pest Control', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Carpet Cleaning', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Aircon Service', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Locksmith', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Roofing', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Masonry', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Tiling', 'created_at' => $now, 'updated_at' => $now],
            ['service_name' => 'Moving & Hauling', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('services')->insert($services);
    }
}
