<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Competidor;
use App\Models\InscripcionCompetidor;
use App\Models\InscripcionCompetidorModalidad;
use App\Models\InscripcionOrganizacion;
use App\Models\Torneo;
use Illuminate\Database\Seeder;

class InscripcionPruebaKataKumiteSeeder extends Seeder
{
    public function run(): void
    {
        $torneo = Torneo::latest('id')->first();

        if (! $torneo) {
            return;
        }

        $modalidadIds = $torneo->modalidades()
            ->whereIn('nombre', ['Kata Individual', 'Kumite Individual'])
            ->pluck('id');

        InscripcionCompetidorModalidad::whereIn('modalidad_id', $modalidadIds)
            ->whereHas('inscripcionCompetidor', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->delete();

        $categorias = $torneo->categorias()
            ->with('modalidad')
            ->whereIn('modalidad_id', $modalidadIds)
            ->orderBy('modalidad_id')
            ->orderBy('edad_desde')
            ->orderBy('edad_hasta')
            ->orderBy('genero')
            ->orderBy('peso_hasta')
            ->get();

        $competidores = Competidor::with(['persona', 'organizacion'])
            ->where('status', 1)
            ->whereHas('persona', function ($query) {
                $query->where('ci', 'like', '900000%');
            })
            ->get()
            ->values();

        $usadosPorModalidad = [];

        foreach ($categorias as $categoria) {
            $asignados = $competidores
                ->reject(function ($competidor) use (&$usadosPorModalidad, $categoria) {
                    return in_array($competidor->id, $usadosPorModalidad[$categoria->modalidad_id] ?? [], true);
                })
                ->take(2);

            foreach ($asignados as $competidor) {
                $this->prepararCompetidorParaCategoria($competidor, $categoria);
                $this->inscribirCompetidor($torneo, $competidor, $categoria);

                $usadosPorModalidad[$categoria->modalidad_id][] = $competidor->id;
            }
        }
    }

    private function prepararCompetidorParaCategoria(Competidor $competidor, Categoria $categoria): void
    {
        $edad = $categoria->edad_desde ?? $categoria->edad_hasta ?? 18;

        $competidor->persona->update([
            'gender' => $categoria->genero && $categoria->genero !== 'Mixto'
                ? $categoria->genero
                : $competidor->persona->gender,
            'birth_date' => now()->subYears((int) $edad)->subMonth()->toDateString(),
        ]);

        if ($this->categoriaRequierePeso($categoria)) {
            $competidor->update([
                'peso' => $this->pesoParaCategoria($categoria),
            ]);
        }
    }

    private function inscribirCompetidor(Torneo $torneo, Competidor $competidor, Categoria $categoria): void
    {
        $inscripcionOrganizacion = InscripcionOrganizacion::firstOrCreate(
            [
                'torneo_id' => $torneo->id,
                'organizacion_id' => $competidor->organizacion_id,
            ],
            [
                'costo' => $torneo->costo_inscripcion_organizacion ?? 0,
            ]
        );

        $inscripcionCompetidor = InscripcionCompetidor::firstOrCreate(
            [
                'torneo_id' => $torneo->id,
                'persona_id' => $competidor->persona_id,
            ],
            [
                'inscripcion_organizacion_id' => $inscripcionOrganizacion->id,
            ]
        );

        $inscripcionCompetidor->modalidades()->firstOrCreate(
            [
                'modalidad_id' => $categoria->modalidad_id,
            ],
            [
                'categoria_id' => $categoria->id,
                'costo' => $torneo->costo_inscripcion_competidor ?? 0,
            ]
        );
    }

    private function categoriaRequierePeso(Categoria $categoria): bool
    {
        return str_contains(mb_strtolower((string) $categoria->modalidad->nombre), 'kumite')
            && $categoria->peso_hasta !== null;
    }

    private function categoriaPesoEsMinimo(Categoria $categoria): bool
    {
        $nombre = mb_strtolower((string) $categoria->nombre);

        return str_contains($nombre, 'mayor o igual') || str_contains($nombre, '>=');
    }

    private function pesoParaCategoria(Categoria $categoria): float
    {
        $referencia = (float) $categoria->peso_hasta;

        if ($this->categoriaPesoEsMinimo($categoria)) {
            return $referencia + 2;
        }

        return max(1, $referencia - 1);
    }
}
