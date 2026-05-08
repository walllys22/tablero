<?php

namespace Database\Seeders;

use App\Models\Modalidad;
use App\Models\Torneo;
use Illuminate\Database\Seeder;

class ModalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalidades = [
            ['nombre' => 'Kumite Individual', 'genero' => 'Masculino'],
            ['nombre' => 'Kumite Equipos', 'genero' => 'Masculino'],
            ['nombre' => 'Kumite Individual', 'genero' => 'Femenino'],
            ['nombre' => 'Kumite Equipos', 'genero' => 'Femenino'],
        ];

        Torneo::query()->each(function (Torneo $torneo) use ($modalidades) {
            foreach ($modalidades as $modalidad) {
                Modalidad::firstOrCreate([
                    'torneo_id' => $torneo->id,
                    'nombre' => $modalidad['nombre'],
                    'genero' => $modalidad['genero'],
                ]);
            }
        });
    }
}
