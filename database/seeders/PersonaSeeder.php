<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Persona con 11 años (el mayor en este seeder)
        Persona::updateOrCreate(
            ['ci' => '3190582'],
            [
                'first_name' => 'Walter Landivar Limpias',
                'birth_date' => now()->subYears(11)->format('Y-m-d'),
                'email' => 'walter@example.com',
                'phone' => '72841511',
                'address' => 'Bolivia',
                'gender' => 'Masculino',
                'sangre' => 'O Rh (-)',
                'status' => 1,
            ]
        );

        // Persona con 6 años (el menor en este seeder)
        Persona::updateOrCreate(
            ['ci' => '88888888'],
            [
                'first_name' => 'Competidor Junior',
                'birth_date' => now()->subYears(6)->format('Y-m-d'),
                'email' => 'junior@example.com',
                'phone' => '70000000',
                'address' => 'Bolivia',
                'gender' => 'Masculino',
                'sangre' => 'O Rh (-)',
                'status' => 1,
            ]
        );
    }
}
