<?php

namespace Database\Seeders;

use App\Models\SistemaCompetencia;
use Illuminate\Database\Seeder;

class SistemaCompetenciaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['WKF', 'Shorinkan'] as $nombre) {
            SistemaCompetencia::updateOrCreate(
                ['nombre' => $nombre],
                ['estado' => 'Activo']
            );
        }
    }
}
