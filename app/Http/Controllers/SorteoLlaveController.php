<?php

namespace App\Http\Controllers;

use App\Models\InscripcionCompetidorModalidad;
use App\Models\SorteoLlave;
use App\Models\Torneo;
use Illuminate\Http\Request;

class SorteoLlaveController extends Controller
{
    public function index(Request $request, Torneo $torneo)
    {
        [$modalidades, $categoriaIdsConCompetidores] = $this->modalidadesDisponiblesParaSorteo($torneo);

        $categoria = null;
        $competidores = collect();
        $llaves = [];
        $seed = (int) $request->input('seed', random_int(100000, 999999));
        $sorteos = SorteoLlave::with(['modalidad', 'categoria'])
            ->where('torneo_id', $torneo->id)
            ->latest()
            ->get();
        $this->asignarAreaUnica($torneo, $sorteos);
        $sorteoActual = null;

        if ($request->filled('categoria_id')) {
            $categoria = $torneo->categorias()
                ->with('modalidad')
                ->where('modalidad_id', $request->input('modalidad_id'))
                ->whereIn('id', $categoriaIdsConCompetidores)
                ->find($request->input('categoria_id'));

            if ($categoria) {
                $competidores = $this->competidoresCategoria($torneo, (int) $categoria->id);
                $sorteoActual = SorteoLlave::where('torneo_id', $torneo->id)
                    ->where('categoria_id', $categoria->id)
                    ->first();

                if ($sorteoActual && ! $request->boolean('sortear')) {
                    $seed = $sorteoActual->seed;
                    $llaves = $sorteoActual->llaves;
                }

                if ($request->boolean('sortear') && ! $sorteoActual && $competidores->count() >= 2) {
                    $llaves = $this->crearLlaves($competidores, $seed);
                    $sorteoActual = SorteoLlave::create([
                        'torneo_id' => $torneo->id,
                        'modalidad_id' => $categoria->modalidad_id,
                        'categoria_id' => $categoria->id,
                        'seed' => $seed,
                        'llaves' => $llaves,
                        'area' => ((int) ($torneo->cantidad_areas ?? 1)) === 1 ? 1 : null,
                    ]);
                    $sorteos = SorteoLlave::with(['modalidad', 'categoria'])
                        ->where('torneo_id', $torneo->id)
                        ->latest()
                        ->get();
                    $this->asignarAreaUnica($torneo, $sorteos);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Categoria sorteada correctamente.',
                        ]);
                    }

                    return redirect()
                        ->route('sorteo-llaves.index', $torneo)
                        ->with('status', 'Categoria sorteada correctamente.');
                }
            }
        }

        return view('sorteo_llaves.browse', compact('torneo', 'modalidades', 'categoria', 'competidores', 'llaves', 'seed', 'sorteos', 'sorteoActual'));
    }

    public function categoriasDisponibles(Torneo $torneo)
    {
        [$modalidades] = $this->modalidadesDisponiblesParaSorteo($torneo);

        return response()->json([
            'modalidades' => $modalidades->map(function ($modalidad) {
                return [
                    'id' => $modalidad->id,
                    'nombre' => $modalidad->nombre,
                    'categorias' => $modalidad->categorias->map(function ($categoria) {
                        return [
                            'id' => $categoria->id,
                            'nombre' => $categoria->nombre,
                            'modalidad_id' => $categoria->modalidad_id,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    public function graphic(Request $request, Torneo $torneo)
    {
        $request->validate([
            'modalidad_id' => ['required'],
            'categoria_id' => ['required'],
            'seed' => ['required', 'integer'],
        ]);

        $sorteo = SorteoLlave::with(['modalidad', 'categoria.modalidad'])
            ->where('torneo_id', $torneo->id)
            ->where('categoria_id', $request->input('categoria_id'))
            ->first();

        $categoria = $sorteo
            ? $sorteo->categoria
            : $torneo->categorias()
                ->with('modalidad')
                ->where('modalidad_id', $request->input('modalidad_id'))
                ->findOrFail($request->input('categoria_id'));

        $competidores = $this->competidoresCategoria($torneo, (int) $categoria->id);
        $llaves = $sorteo
            ? $sorteo->llaves
            : ($competidores->count() >= 2 ? $this->crearLlaves($competidores, (int) $request->input('seed')) : []);

        return view('sorteo_llaves.graphic', compact('torneo', 'categoria', 'competidores', 'llaves'));
    }

    public function updateArea(Request $request, Torneo $torneo, SorteoLlave $sorteo)
    {
        abort_unless((int) $sorteo->torneo_id === (int) $torneo->id, 404);

        $data = $request->validate([
            'area' => ['required', 'integer', 'min:1', 'max:' . max(1, (int) $torneo->cantidad_areas)],
        ]);

        $sorteo->update([
            'area' => $data['area'],
        ]);

        return redirect()
            ->route('sorteo-llaves.index', $torneo)
            ->with('status', 'Area asignada correctamente.');
    }

    public function destroy(Torneo $torneo, SorteoLlave $sorteo)
    {
        abort_unless((int) $sorteo->torneo_id === (int) $torneo->id, 404);

        $sorteo->delete();

        return redirect()
            ->route('sorteo-llaves.index', $torneo)
            ->with('status', 'Sorteo eliminado correctamente. Puede sortear la categoria nuevamente.');
    }

    private function competidoresCategoria(Torneo $torneo, int $categoriaId)
    {
        return InscripcionCompetidorModalidad::query()
            ->with([
                'inscripcionCompetidor.persona',
                'inscripcionCompetidor.inscripcionOrganizacion.organizacion',
            ])
            ->where('categoria_id', $categoriaId)
            ->whereHas('inscripcionCompetidor', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->get()
            ->map(function ($detalle) {
                $competidor = $detalle->inscripcionCompetidor;
                $organizacion = $competidor->inscripcionOrganizacion->organizacion;

                return [
                    'id' => $competidor->id,
                    'nombre' => $competidor->persona->first_name ?? 'Sin nombre',
                    'organizacion_id' => $organizacion->id ?? null,
                    'organizacion' => $organizacion->nombre ?? 'Sin organizacion',
                ];
            })
            ->unique('id')
            ->values();
    }

    private function asignarAreaUnica(Torneo $torneo, $sorteos): void
    {
        if ((int) ($torneo->cantidad_areas ?? 1) !== 1) {
            return;
        }

        $sorteosSinArea = $sorteos->filter(fn ($sorteo) => ! $sorteo->area);

        if ($sorteosSinArea->isEmpty()) {
            return;
        }

        SorteoLlave::whereIn('id', $sorteosSinArea->pluck('id'))->update(['area' => 1]);

        $sorteosSinArea->each(function ($sorteo) {
            $sorteo->area = 1;
        });
    }

    private function modalidadesDisponiblesParaSorteo(Torneo $torneo): array
    {
        $categoriasSorteadas = SorteoLlave::where('torneo_id', $torneo->id)
            ->pluck('categoria_id');
        $categoriaIdsConCompetidores = InscripcionCompetidorModalidad::query()
            ->whereHas('inscripcionCompetidor', function ($query) use ($torneo) {
                $query->where('torneo_id', $torneo->id);
            })
            ->whereNotIn('categoria_id', $categoriasSorteadas)
            ->select('categoria_id')
            ->groupBy('categoria_id')
            ->havingRaw('COUNT(DISTINCT inscripcion_competidor_id) >= 2')
            ->pluck('categoria_id');

        $modalidades = $torneo->modalidades()
            ->whereHas('categorias', function ($query) use ($categoriaIdsConCompetidores) {
                $query->whereIn('categorias.id', $categoriaIdsConCompetidores);
            })
            ->with(['categorias' => function ($query) use ($categoriaIdsConCompetidores) {
                $query->whereIn('categorias.id', $categoriaIdsConCompetidores)
                    ->orderBy('edad_desde')
                    ->orderBy('edad_hasta')
                    ->orderBy('genero')
                    ->orderBy('peso_hasta')
                    ->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        return [$modalidades, $categoriaIdsConCompetidores];
    }

    private function crearLlaves($competidores, int $seed): array
    {
        $total = $competidores->count();

        if ($total === 0) {
            return [];
        }

        $tamanoLlave = $this->siguientePotenciaDos($total);
        $byes = $tamanoLlave - $total;
        $pendientes = $this->ordenarSorteo($competidores, $seed, 'competidores');
        $primeraRonda = collect();

        for ($i = 0; $i < $byes; $i++) {
            $primeraRonda->push([
                'a' => $pendientes->shift(),
                'b' => null,
                'bye' => true,
            ]);
        }

        while ($pendientes->isNotEmpty()) {
            $a = $pendientes->shift();
            $opponentIndex = $pendientes->search(function ($competidor) use ($a) {
                return $competidor['organizacion_id'] !== $a['organizacion_id'];
            });

            if ($opponentIndex === false) {
                $opponentIndex = 0;
            }

            $b = $pendientes->pull($opponentIndex);
            $pendientes = $pendientes->values();

            $primeraRonda->push([
                'a' => $a,
                'b' => $b,
                'bye' => false,
            ]);
        }

        $rondas = [[
            'nombre' => $this->nombreRonda($tamanoLlave),
            'combates' => $this->ordenarSorteo($primeraRonda, $seed, 'combates'),
        ]];

        $combates = (int) ($tamanoLlave / 4);
        while ($combates >= 1) {
            $rondas[] = [
                'nombre' => $combates === 1 ? 'Final' : $this->nombreRonda($combates * 2),
                'combates' => collect(range(1, $combates))->map(function () {
                    return [
                        'a' => null,
                        'b' => null,
                        'bye' => false,
                    ];
                }),
            ];

            $combates = (int) floor($combates / 2);
        }

        return $rondas;
    }

    private function ordenarSorteo($items, int $seed, string $salt)
    {
        return $items
            ->sortBy(function ($item, $index) use ($seed, $salt) {
                $id = is_array($item) && isset($item['id'])
                    ? $item['id']
                    : ($item['a']['id'] ?? $index);

                return sprintf('%u', crc32($seed . '-' . $salt . '-' . $id . '-' . $index));
            })
            ->values();
    }

    private function siguientePotenciaDos(int $number): int
    {
        $power = 1;

        while ($power < $number) {
            $power *= 2;
        }

        return $power;
    }

    private function nombreRonda(int $tamano): string
    {
        return match ($tamano) {
            2 => 'Final',
            4 => 'Semifinal',
            8 => 'Cuartos de final',
            16 => 'Octavos de final',
            32 => 'Dieciseisavos de final',
            default => 'Ronda de ' . $tamano,
        };
    }
}
