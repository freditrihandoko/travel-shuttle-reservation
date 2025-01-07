<?php

namespace Database\Seeders;

use App\Models\Pool;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PoolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pool::insert([
            ['city_id' => 1, 'name' => 'Jakarta Terminal', 'address' => 'Jl. Terminal Jakarta'],
            ['city_id' => 2, 'name' => 'Bandung Terminal', 'address' => 'Jl. Terminal Bandung'],
            ['city_id' => 3, 'name' => 'Surabaya Terminal', 'address' => 'Jl. Terminal Surabaya'],
            ['city_id' => 4, 'name' => 'Yogyakarta Terminal', 'address' => 'Jl. Terminal Yogyakarta'],
            ['city_id' => 5, 'name' => 'Bali Terminal', 'address' => 'Jl. Terminal Bali'],
        ]);
    }
}
