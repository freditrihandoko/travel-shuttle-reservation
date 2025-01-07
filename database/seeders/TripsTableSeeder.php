<?php

namespace Database\Seeders;

use App\Models\Trip;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TripsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Trip::insert([
            ['route_id' => 1, 'departure_time' => '2024-08-01 08:00:00', 'price' => 200000],
            ['route_id' => 2, 'departure_time' => '2024-08-01 10:00:00', 'price' => 250000],
            ['route_id' => 3, 'departure_time' => '2024-08-01 12:00:00', 'price' => 300000],
            ['route_id' => 4, 'departure_time' => '2024-08-01 14:00:00', 'price' => 350000],
            ['route_id' => 5, 'departure_time' => '2024-08-01 16:00:00', 'price' => 400000],
        ]);
    }
}
