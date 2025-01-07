<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Route::insert([
            ['from_pool_id' => 1, 'to_pool_id' => 2],
            ['from_pool_id' => 2, 'to_pool_id' => 3],
            ['from_pool_id' => 3, 'to_pool_id' => 4],
            ['from_pool_id' => 4, 'to_pool_id' => 5],
            ['from_pool_id' => 1, 'to_pool_id' => 5],
        ]);
    }
}
