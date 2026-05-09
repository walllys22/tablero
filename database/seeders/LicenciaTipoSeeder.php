<?php

namespace Database\Seeders;

use App\Models\LicenciaTipo;
use Illuminate\Database\Seeder;

class LicenciaTipoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Boliviana', 'Panamericana', 'Sudamericana', 'Mundial', 'Argentina'] as $nombre) {
            LicenciaTipo::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
