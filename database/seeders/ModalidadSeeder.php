<?php

namespace Database\Seeders;

use App\Models\Torneo;
use App\Support\DefaultTournamentCatalog;
use Illuminate\Database\Seeder;

class ModalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Torneo::query()->each(function (Torneo $torneo) {
            DefaultTournamentCatalog::seedFor($torneo);
        });
    }
}
