<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat peran
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'trip-admin']);
        Role::create(['name' => 'reservation-admin']);

        // Buat izin
        Permission::create(['name' => 'manage trips']);
        Permission::create(['name' => 'manage reservations']);

        // Tetapkan izin ke peran
        $tripAdmin = Role::findByName('trip-admin');
        $tripAdmin->givePermissionTo('manage trips');

        $reservationAdmin = Role::findByName('reservation-admin');
        $reservationAdmin->givePermissionTo('manage reservations');
    }
}
