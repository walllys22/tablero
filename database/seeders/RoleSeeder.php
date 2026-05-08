<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::updateOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrador del sistema']
        );

        \App\Models\Role::updateOrCreate(
            ['name' => 'user'],
            ['description' => 'Usuario regular']
        );

        \App\Models\Role::updateOrCreate(
            ['name' => 'organizer'],
            ['description' => 'Organizador de torneos']
        );
    }
}
