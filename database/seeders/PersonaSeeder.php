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
        Persona::updateOrCreate(
            ['ci' => '12345678'],
            [
                'first_name' => 'Walter Landivar Limpias',
                'birth_date' => '1985-01-01',
                'email' => 'walter@example.com',
                'country_code' => '591',
                'phone' => '70000000',
                'address' => 'Bolivia',
                'gender' => 'Masculino',
                'sangre' => 'O+',
                'status' => 1,
            ]
        );
    }
}
