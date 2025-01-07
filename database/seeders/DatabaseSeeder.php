<?php

namespace Database\Seeders;

use App\Models\City;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Pool;
use App\Models\Trip;
use App\Models\User;
use App\Models\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'),

        ]);

        $this->call([
            CitiesTableSeeder::class,
            PoolsTableSeeder::class,
            RoutesTableSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
