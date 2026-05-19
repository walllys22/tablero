<?php

namespace App\Http\Controllers;

use App\Models\KumiteCombateResultado;
use App\Models\KumitePodio;
use App\Models\Kata;
use App\Models\KataCombateResultado;
use App\Models\SorteoLlave;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TableroController extends Controller
{
    public function kumite(Request $request)
    {
        $sorteo = $this->sorteoKumite($request);
        $llaves = $sorteo ? $this->llavesConResultados($sorteo) : [];
        $combatesKumite = [];
        $numeroLlave = 1;

        foreach ($llaves as $roundIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                $esBye = (bool) ($combate['bye'] ?? false);
                $rojo = $combate['a']['nombre'] ?? '';
                $azul = $combate['b']['nombre'] ?? '';

                if ($esBye && $rojo === '' && $azul === '') {
                    $azul = 'BYE';
                }

                $combatesKumite[] = [
                    'indice' => count($combatesKumite),
                    'round_index' => $roundIndex,
                    'match_index' => $matchIndex,
                    'ronda' => $ronda['nombre'] ?? 'Combate',
                    'numero_llave' => $numeroLlave++,
                    'rojo' => $rojo,
                    'rojo_organizacion' => $combate['a']['organizacion'] ?? '',
                    'azul' => $azul,
                    'azul_organizacion' => $combate['b']['organizacion'] ?? '',
                    'bye' => $esBye,
                    'realizado' => (bool) ($combate['realizado'] ?? false),
                    'ganador' => $combate['ganador']['nombre'] ?? null,
                ];
            }
        }

        $primerIndicePendiente = $this->primerIndiceCombatePendiente($combatesKumite);
        $primerCombate = $primerIndicePendiente !== null ? ($combatesKumite[$primerIndicePendiente] ?? []) : [];

        $combateInicialKumite = [
            'sorteo_id' => $sorteo?->id,
            'siguiente_sorteo_id' => $this->siguienteSorteoKumite($sorteo)?->id,
            'modalidad' => $sorteo?->modalidad?->nombre ?? 'Kumite Individual',
            'categoria' => $sorteo?->categoria?->nombre ?? '',
            'indice_combate' => $primerIndicePendiente,
            'rojo' => $primerCombate['rojo'] ?? '',
            'azul' => $primerCombate['azul'] ?? '',
        ];

        return view('kumite.tablero', compact('combateInicialKumite', 'combatesKumite'));
    }

    public function kata(Request $request)
    {
        $tableroKata = $this->tableroKataInicial($request);
        $sistemaCompetenciaId = $tableroKata['sistema_competencia_id'] ?? null;
        $katasQuery = Kata::query()
            ->where('estado', 'Activo');

        if ($sistemaCompetenciaId) {
            $katasQuery->where('sistema_id', $sistemaCompetenciaId);
        }

        $katas = $katasQuery
            ->orderByRaw('CASE WHEN numero IS NULL THEN 1 ELSE 0 END')
            ->orderBy('numero')
            ->orderBy('nombre')
            ->get(['id', 'numero', 'nombre'])
            ->values()
            ->map(function ($kata, $index) {
                $kata->numero_tablero = $kata->numero ?: ($index + 1);

                return $kata;
            });

        return view('kata.tablero', compact('tableroKata', 'katas'));
    }

    public function resultadoKata(Request $request)
    {
        $resultadoKata = [
            'sorteo_id' => $request->query('sorteo_id'),
            'color' => $request->query('color', 'rojo'),
            'nombre' => $request->query('nombre', 'Walter Landivar Limpias'),
            'kata_numero' => $request->query('kata_numero', '1'),
            'kata_nombre' => $request->query('kata_nombre', 'Anan'),
            'puntaje' => (int) $request->query('puntaje', 3),
            'banderas_rojas' => (int) $request->query('banderas_rojas', 3),
            'banderas_azules' => (int) $request->query('banderas_azules', 2),
            'kiken_rojo' => filter_var($request->query('kiken_rojo', false), FILTER_VALIDATE_BOOLEAN),
            'kiken_azul' => filter_var($request->query('kiken_azul', false), FILTER_VALIDATE_BOOLEAN),
        ];

        return view('kata.resultado', compact('resultadoKata'));
    }

    public function podioKata(Request $request)
    {
        $sorteo = null;

        if ($request->filled('sorteo_id')) {
            $sorteo = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKata'])
                ->whereHas('modalidad', fn ($query) => $query->where('nombre', 'like', '%Kata%'))
                ->findOrFail($request->query('sorteo_id'));
            $llaves = $this->llavesConResultadosKata($sorteo);
            $podio = $this->calcularPodioKata($llaves);
            $modalidad = $sorteo->modalidad->nombre ?? 'Kata Individual';
            $categoria = $sorteo->categoria->nombre ?? '';

            return view('kata.podio', compact('podio', 'modalidad', 'categoria', 'sorteo'));
        }

        $podio = [
            'oro' => trim((string) $request->query('oro', '')),
            'plata' => trim((string) $request->query('plata', '')),
            'bronce_1' => trim((string) $request->query('bronce_1', '')),
            'bronce_2' => trim((string) $request->query('bronce_2', '')),
        ];
        $modalidad = $request->query('modalidad', 'Kata Individual');
        $categoria = $request->query('categoria', '');

        return view('kata.podio', compact('podio', 'modalidad', 'categoria', 'sorteo'));
    }

    public function guardarCombateKata(Request $request)
    {
        $data = $request->validate([
            'sorteo_id' => ['nullable', 'exists:sorteo_llaves,id'],
            'indice_combate' => ['required', 'integer', 'min:0'],
            'competidor_rojo' => ['nullable', 'string', 'max:255'],
            'competidor_azul' => ['nullable', 'string', 'max:255'],
            'kata_numero_rojo' => ['nullable', 'string', 'max:50'],
            'kata_numero_azul' => ['nullable', 'string', 'max:50'],
            'kata_nombre_rojo' => ['nullable', 'string', 'max:255'],
            'kata_nombre_azul' => ['nullable', 'string', 'max:255'],
            'puntaje_rojo' => ['required', 'integer', 'min:0'],
            'puntaje_azul' => ['required', 'integer', 'min:0'],
            'kiken_rojo' => ['required', 'boolean'],
            'kiken_azul' => ['required', 'boolean'],
            'ganador' => ['nullable', 'string', 'max:255'],
            'ganador_color' => ['nullable', 'in:rojo,azul'],
            'realizado_at' => ['nullable', 'date'],
        ]);

        if ((int) $data['puntaje_rojo'] === 0 && (int) $data['puntaje_azul'] === 0) {
            return response()->json([
                'message' => 'Uno de los Competidores tienen que tener puntaje',
            ], 422);
        }

        $ganadorColor = $data['ganador_color'] ?? null;
        if ((int) $data['puntaje_rojo'] !== (int) $data['puntaje_azul']) {
            $ganadorColor = (int) $data['puntaje_rojo'] > (int) $data['puntaje_azul'] ? 'rojo' : 'azul';
        } elseif ($this->normalizarNombreCompetidor($data['ganador'] ?? null) === $this->normalizarNombreCompetidor($data['competidor_rojo'] ?? null)) {
            $ganadorColor = 'rojo';
        } elseif ($this->normalizarNombreCompetidor($data['ganador'] ?? null) === $this->normalizarNombreCompetidor($data['competidor_azul'] ?? null)) {
            $ganadorColor = 'azul';
        }

        $ganador = $data['ganador'] ?? null;
        if ($ganadorColor === 'rojo' && ! empty($data['competidor_rojo'])) {
            $ganador = $data['competidor_rojo'];
        } elseif ($ganadorColor === 'azul' && ! empty($data['competidor_azul'])) {
            $ganador = $data['competidor_azul'];
        }

        $resultado = KataCombateResultado::updateOrCreate(
            [
                'sorteo_llave_id' => $data['sorteo_id'] ?? null,
                'indice_combate' => $data['indice_combate'],
            ],
            [
                'competidor_rojo' => $data['competidor_rojo'] ?? null,
                'competidor_azul' => $data['competidor_azul'] ?? null,
                'kata_numero_rojo' => $data['kata_numero_rojo'] ?? null,
                'kata_numero_azul' => $data['kata_numero_azul'] ?? null,
                'kata_nombre_rojo' => $data['kata_nombre_rojo'] ?? null,
                'kata_nombre_azul' => $data['kata_nombre_azul'] ?? null,
                'puntaje_rojo' => $data['puntaje_rojo'],
                'puntaje_azul' => $data['puntaje_azul'],
                'kiken_rojo' => $data['kiken_rojo'],
                'kiken_azul' => $data['kiken_azul'],
                'ganador' => $ganador,
                'ganador_color' => $ganadorColor,
                'realizado_at' => ! empty($data['realizado_at']) ? Carbon::parse($data['realizado_at']) : Carbon::now(),
            ]
        );

        return response()->json([
            'success' => true,
            'resultado_id' => $resultado->id,
        ]);
    }

    public function eliminarCombateKata(Request $request)
    {
        $data = $request->validate([
            'sorteo_id' => ['required', 'exists:sorteo_llaves,id'],
            'indice_combate' => ['required', 'integer', 'min:0'],
        ]);

        KataCombateResultado::where('sorteo_llave_id', $data['sorteo_id'])
            ->where('indice_combate', $data['indice_combate'])
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function podioKumite(Request $request)
    {
        $sorteo = $this->sorteoKumite($request);
        abort_unless($sorteo, 404);

        $llaves = $this->llavesConResultados($sorteo);
        $podio = $this->calcularPodio($llaves, $sorteo);
        $this->guardarPodioKumite($sorteo, $podio);
        $siguienteSorteo = $this->siguienteSorteoKumite($sorteo);

        return view('kumite.podio', compact('sorteo', 'podio', 'siguienteSorteo'));
    }

    public function guardarCombateKumite(Request $request)
    {
        $data = $request->validate([
            'sorteo_id' => ['required', 'exists:sorteo_llaves,id'],
            'numero_llave' => ['required', 'integer', 'min:1'],
            'indice_combate' => ['required', 'integer', 'min:0'],
            'competidor_rojo' => ['nullable', 'string', 'max:255'],
            'competidor_azul' => ['nullable', 'string', 'max:255'],
            'puntaje_rojo' => ['required', 'integer', 'min:0'],
            'puntaje_azul' => ['required', 'integer', 'min:0'],
            'faltas_rojo' => ['array'],
            'faltas_azul' => ['array'],
            'senshu' => ['nullable', 'in:rojo,azul'],
            'senshu_rojo' => ['required', 'boolean'],
            'senshu_azul' => ['required', 'boolean'],
            'kiken_rojo' => ['required', 'boolean'],
            'kiken_azul' => ['required', 'boolean'],
            'tecnicas_rojo' => ['array'],
            'tecnicas_azul' => ['array'],
            'ganador' => ['nullable', 'string', 'max:255'],
            'ganador_color' => ['nullable', 'in:rojo,azul'],
            'round_index' => ['required', 'integer', 'min:0'],
            'match_index' => ['required', 'integer', 'min:0'],
        ]);

        $sorteo = SorteoLlave::findOrFail($data['sorteo_id']);

        $resultado = KumiteCombateResultado::updateOrCreate(
            [
                'sorteo_llave_id' => $sorteo->id,
                'indice_combate' => $data['indice_combate'],
            ],
            [
                'numero_llave' => $data['numero_llave'],
                'competidor_rojo' => $data['competidor_rojo'] ?? null,
                'competidor_azul' => $data['competidor_azul'] ?? null,
                'puntaje_rojo' => $data['puntaje_rojo'],
                'puntaje_azul' => $data['puntaje_azul'],
                'faltas_rojo' => $data['faltas_rojo'] ?? [],
                'faltas_azul' => $data['faltas_azul'] ?? [],
                'senshu' => $data['senshu'] ?? null,
                'senshu_rojo' => $data['senshu_rojo'],
                'senshu_azul' => $data['senshu_azul'],
                'kiken_rojo' => $data['kiken_rojo'],
                'kiken_azul' => $data['kiken_azul'],
                'tecnicas_rojo' => $data['tecnicas_rojo'] ?? [],
                'tecnicas_azul' => $data['tecnicas_azul'] ?? [],
                'ganador' => $data['ganador'] ?? null,
                'ganador_color' => $data['ganador_color'] ?? null,
                'realizado_at' => Carbon::now(),
            ]
        );

        $this->marcarCombateRealizado($sorteo, $data);

        return response()->json([
            'success' => true,
            'resultado_id' => $resultado->id,
        ]);
    }

    private function sorteoKumite(Request $request): ?SorteoLlave
    {
        if ($request->filled('sorteo_id')) {
            return SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
                ->whereHas('modalidad', fn ($query) => $this->filtrarModalidadKumite($query))
                ->find($request->input('sorteo_id'));
        }

        $sorteos = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
            ->whereHas('modalidad', fn ($query) => $this->filtrarModalidadKumite($query))
            ->orderByRaw('COALESCE(area, 999)')
            ->orderByRaw('COALESCE(orden, 999999)')
            ->orderBy('id')
            ->get();

        return $sorteos->first(function ($sorteo) {
            return $this->tieneCombatesPendientes($sorteo);
        }) ?: SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
            ->whereHas('modalidad', fn ($query) => $this->filtrarModalidadKumite($query))
            ->latest()
            ->first();
    }

    private function siguienteSorteoKumite(?SorteoLlave $actual): ?SorteoLlave
    {
        if (! $actual) {
            return null;
        }

        $query = SorteoLlave::with(['modalidad', 'categoria', 'resultadosKumite'])
            ->where('torneo_id', $actual->torneo_id)
            ->where('id', '!=', $actual->id)
            ->whereHas('modalidad', fn ($query) => $this->filtrarModalidadKumite($query))
            ->orderByRaw('CASE WHEN COALESCE(orden, 999999) > COALESCE(?, 999999) THEN 0 ELSE 1 END', [$actual->orden])
            ->orderByRaw('COALESCE(orden, 999999)')
            ->orderBy('id');

        if ($actual->area) {
            $query->where('area', $actual->area);
        }

        return $query->get()->first(function ($sorteo) {
            return $this->tieneCombatesPendientes($sorteo);
        });
    }

    private function filtrarModalidadKumite($query): void
    {
        $query->where('nombre', 'like', '%Kumite%');
    }

    private function sorteoKata(Request $request): ?SorteoLlave
    {
        if ($request->filled('sorteo_id')) {
            return SorteoLlave::with(['modalidad', 'categoria', 'torneo', 'resultadosKata'])
                ->whereHas('modalidad', fn ($query) => $query->where('nombre', 'like', '%Kata%'))
                ->find($request->input('sorteo_id'));
        }

        $sorteos = SorteoLlave::with(['modalidad', 'categoria', 'torneo', 'resultadosKata'])
            ->whereHas('modalidad', fn ($query) => $query->where('nombre', 'like', '%Kata%'))
            ->orderByRaw('COALESCE(area, 999)')
            ->orderByRaw('COALESCE(orden, 999999)')
            ->orderBy('id')
            ->get();

        return $sorteos->first(function ($sorteo) {
            return $this->tieneCombatesPendientesKata($sorteo);
        }) ?: $sorteos->last();
    }

    private function tableroKataInicial(Request $request): array
    {
        $sorteo = $this->sorteoKata($request);

        if (! $sorteo) {
            return [
                'modalidad' => 'Kata Individual',
                'categoria' => '',
                'combate' => ['rojo' => '', 'azul' => ''],
                'proximo' => ['rojo' => '', 'azul' => ''],
                'llaves' => [],
                'resultados_version' => 0,
                'sistema_competencia_id' => null,
            ];
        }

        $llaves = $this->llavesConResultadosKata($sorteo);
        $combates = $this->combatesKataDisponibles($llaves);
        $indiceCombate = collect($combates)->search(fn ($combate) => ! $combate['bye'] && ! $combate['realizado']);
        $ultimoResultadoKata = $sorteo->resultadosKata->max('updated_at');
        $versionLlavesKata = collect([$ultimoResultadoKata, $sorteo->updated_at])
            ->filter()
            ->map(fn ($fecha) => Carbon::parse($fecha)->timestamp)
            ->max();
        $sinCombatesPendientes = $indiceCombate === false;

        $combateActual = $sinCombatesPendientes
            ? ['rojo' => '********', 'azul' => '********']
            : ($combates[$indiceCombate] ?? ['rojo' => '********', 'azul' => '********']);
        $proximoCombate = $sinCombatesPendientes
            ? ['rojo' => '********', 'azul' => '********']
            : ($combates[$indiceCombate + 1] ?? ['rojo' => '********', 'azul' => '********']);

        return [
            'modalidad' => $sorteo->modalidad->nombre ?? 'Kata Individual',
            'categoria' => $sorteo->categoria->nombre ?? '',
            'combate' => $combateActual,
            'proximo' => $proximoCombate,
            'sorteo_id' => $sorteo->id,
            'llaves' => $llaves,
            'sin_combates_pendientes' => $sinCombatesPendientes,
            'resultados_version' => $versionLlavesKata ?: 0,
            'sistema_competencia_id' => $sorteo->torneo?->sistema_competencia,
        ];
    }

    private function combatesKataDisponibles(array $llaves): array
    {
        $combates = [];

        foreach ($llaves as $ronda) {
            foreach (($ronda['combates'] ?? []) as $combate) {
                $rojo = trim((string) ($combate['a']['nombre'] ?? ''));
                $azul = trim((string) ($combate['b']['nombre'] ?? ''));
                if ($rojo === '' && $azul === '') {
                    continue;
                }

                $combates[] = [
                    'rojo' => $rojo ?: 'BYE',
                    'azul' => $azul ?: 'BYE',
                    'bye' => ($combate['bye'] ?? false) && (($rojo !== '') !== ($azul !== '')),
                    'realizado' => (bool) ($combate['realizado'] ?? false),
                ];
            }
        }

        return $combates;
    }

    private function tieneCombatesPendientesKata(SorteoLlave $sorteo): bool
    {
        foreach ($this->llavesConResultadosKata($sorteo) as $ronda) {
            foreach (($ronda['combates'] ?? []) as $combate) {
                $rojo = trim((string) ($combate['a']['nombre'] ?? ''));
                $azul = trim((string) ($combate['b']['nombre'] ?? ''));
                $byeReal = ($combate['bye'] ?? false) && (($rojo !== '') !== ($azul !== ''));

                if (($combate['realizado'] ?? false) || $byeReal) {
                    continue;
                }

                if ($rojo !== '' && $azul !== '' && strtoupper($rojo) !== 'BYE' && strtoupper($azul) !== 'BYE') {
                    return true;
                }
            }
        }

        return false;
    }

    private function tieneCombatesPendientes(SorteoLlave $sorteo): bool
    {
        foreach ($this->llavesConResultados($sorteo) as $ronda) {
            foreach (($ronda['combates'] ?? []) as $combate) {
                $rojo = trim((string) ($combate['a']['nombre'] ?? ''));
                $azul = trim((string) ($combate['b']['nombre'] ?? ''));
                $byeReal = ($combate['bye'] ?? false) && (($rojo !== '') !== ($azul !== ''));

                if (($combate['realizado'] ?? false) || $byeReal) {
                    continue;
                }

                if ($rojo !== '' && $azul !== '' && strtoupper($rojo) !== 'BYE' && strtoupper($azul) !== 'BYE') {
                    return true;
                }
            }
        }

        return false;
    }

    private function primerIndiceCombatePendiente(array $combates): ?int
    {
        foreach ($combates as $index => $combate) {
            $rojo = trim((string) ($combate['rojo'] ?? ''));
            $azul = trim((string) ($combate['azul'] ?? ''));
            $byeReal = ($combate['bye'] ?? false) && (($rojo !== '') !== ($azul !== ''));

            if (($combate['realizado'] ?? false) || $byeReal) {
                continue;
            }

            if ($rojo !== '' && $azul !== '' && strtoupper($rojo) !== 'BYE' && strtoupper($azul) !== 'BYE') {
                return $index;
            }
        }

        return null;
    }

    private function llavesConResultados(SorteoLlave $sorteo): array
    {
        $llaves = $this->propagarByes($this->reiniciarLlavesBase($sorteo->llaves));

        foreach ($sorteo->resultadosKumite->sortBy('indice_combate') as $resultado) {
            $position = $this->posicionCombatePorIndice($llaves, (int) $resultado->indice_combate);

            if (! $position) {
                continue;
            }

            [$roundIndex, $matchIndex] = $position;
            $combate = $llaves[$roundIndex]['combates'][$matchIndex];
            $ganadorSide = $resultado->ganador_color === 'rojo' ? 'a' : 'b';
            $ganadorCompetidor = $combate[$ganadorSide] ?? [
                'nombre' => $resultado->ganador,
                'organizacion' => '',
                'organizacion_id' => null,
            ];

            $llaves[$roundIndex]['combates'][$matchIndex]['realizado'] = true;
            $llaves[$roundIndex]['combates'][$matchIndex]['ganador'] = [
                'nombre' => $resultado->ganador,
                'color' => $resultado->ganador_color,
                'fecha' => optional($resultado->realizado_at)->toDateTimeString(),
            ];

            $nextRound = $roundIndex + 1;
            $nextMatch = (int) floor($matchIndex / 2);
            $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

            if (isset($llaves[$nextRound]['combates'][$nextMatch])
                && $this->puedePropagarGanadorA($llaves[$nextRound]['combates'][$nextMatch][$nextSide] ?? null)) {
                $llaves[$nextRound]['combates'][$nextMatch][$nextSide] = $ganadorCompetidor;
            }
        }

        return $llaves;
    }

    private function ladoGanadorKata($resultado, array $combate): string
    {
        $ganador = $this->normalizarNombreCompetidor($resultado->ganador ?? null);
        $rojo = $this->normalizarNombreCompetidor(($combate['a']['nombre'] ?? null) ?: ($resultado->competidor_rojo ?? null));
        $azul = $this->normalizarNombreCompetidor(($combate['b']['nombre'] ?? null) ?: ($resultado->competidor_azul ?? null));

        if ($ganador !== '' && $ganador === $rojo) {
            return 'a';
        }

        if ($ganador !== '' && $ganador === $azul) {
            return 'b';
        }

        if ((int) ($resultado->puntaje_rojo ?? 0) !== (int) ($resultado->puntaje_azul ?? 0)) {
            return (int) $resultado->puntaje_rojo > (int) $resultado->puntaje_azul ? 'a' : 'b';
        }

        return ($resultado->ganador_color ?? null) === 'azul' ? 'b' : 'a';
    }

    private function normalizarNombreCompetidor(?string $nombre): string
    {
        return trim(mb_strtolower(preg_replace('/\s+/', ' ', $nombre ?? '')));
    }

    private function llavesConResultadosKata(SorteoLlave $sorteo): array
    {
        $llaves = $this->propagarByes($this->reiniciarLlavesBase($sorteo->llaves ?? []));

        foreach ($sorteo->resultadosKata->sortBy('indice_combate') as $resultado) {
            $position = $this->posicionCombatePorIndice($llaves, (int) $resultado->indice_combate);

            if (! $position) {
                continue;
            }

            [$roundIndex, $matchIndex] = $position;
            $combate = $llaves[$roundIndex]['combates'][$matchIndex];
            $ganadorSide = $this->ladoGanadorKata($resultado, $combate);
            $ganadorCompetidor = $combate[$ganadorSide] ?? [
                'nombre' => $resultado->ganador,
                'organizacion' => '',
                'organizacion_id' => null,
            ];

            $llaves[$roundIndex]['combates'][$matchIndex]['realizado'] = true;
            $llaves[$roundIndex]['combates'][$matchIndex]['ganador'] = [
                'nombre' => $resultado->ganador,
                'color' => $resultado->ganador_color,
                'fecha' => optional($resultado->realizado_at)->toDateTimeString(),
            ];

            $nextRound = $roundIndex + 1;
            $nextMatch = (int) floor($matchIndex / 2);
            $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

            if (isset($llaves[$nextRound]['combates'][$nextMatch])
                && $this->puedePropagarGanadorA($llaves[$nextRound]['combates'][$nextMatch][$nextSide] ?? null)) {
                $llaves[$nextRound]['combates'][$nextMatch][$nextSide] = $ganadorCompetidor;
            }

            $llaves = $this->propagarByes($llaves);
        }

        return $llaves;
    }

    private function calcularPodioKata(array $llaves): array
    {
        $podio = [
            'oro' => '',
            'plata' => '',
            'bronce_1' => '',
            'bronce_2' => '',
        ];

        if (empty($llaves)) {
            return $podio;
        }

        $finalRound = count($llaves) - 1;
        $final = $llaves[$finalRound]['combates'][0] ?? null;

        if ($final && ! empty($final['ganador']['nombre'])) {
            $ganador = $final['ganador']['nombre'];
            $rojo = $this->nombreCompetidorPodio($final['a'] ?? null);
            $azul = $this->nombreCompetidorPodio($final['b'] ?? null);

            $podio['oro'] = $ganador;
            $podio['plata'] = $ganador === $rojo ? $azul : $rojo;
        }

        if (count($llaves) < 2) {
            return $podio;
        }

        $semifinalRound = count($llaves) - 2;
        $bronces = [];

        foreach (($llaves[$semifinalRound]['combates'] ?? []) as $combate) {
            if (empty($combate['ganador']['nombre'])) {
                continue;
            }

            $ganador = $combate['ganador']['nombre'];
            $rojo = $this->nombreCompetidorPodio($combate['a'] ?? null);
            $azul = $this->nombreCompetidorPodio($combate['b'] ?? null);
            $perdedor = $ganador === $rojo ? $azul : $rojo;

            if ($perdedor) {
                $bronces[] = $perdedor;
            }
        }

        $podio['bronce_1'] = $bronces[0] ?? '';
        $podio['bronce_2'] = $bronces[1] ?? '';

        return $podio;
    }

    private function reiniciarLlavesBase(array $llaves): array
    {
        foreach ($llaves as $roundIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                unset(
                    $llaves[$roundIndex]['combates'][$matchIndex]['realizado'],
                    $llaves[$roundIndex]['combates'][$matchIndex]['ganador']
                );

                if ($roundIndex > 0 && ! $this->combateTieneAsignacionManual($combate)) {
                    unset(
                        $llaves[$roundIndex]['combates'][$matchIndex]['a'],
                        $llaves[$roundIndex]['combates'][$matchIndex]['b']
                    );
                }
            }
        }

        return $llaves;
    }

    private function combateTieneAsignacionManual(array $combate): bool
    {
        foreach (['a', 'b'] as $side) {
            $nombre = $combate[$side]['nombre'] ?? '';

            if ($nombre !== '' && ! str_starts_with($nombre, 'Ganador')) {
                return true;
            }
        }

        return false;
    }

    private function puedePropagarGanadorA(?array $competidor): bool
    {
        $nombre = $competidor['nombre'] ?? '';

        return $nombre === '' || str_starts_with($nombre, 'Ganador');
    }

    private function propagarByes(array $llaves): array
    {
        foreach ($llaves as $roundIndex => $ronda) {
            if (! isset($llaves[$roundIndex + 1])) {
                continue;
            }

            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                $tieneRojo = ! empty($combate['a']);
                $tieneAzul = ! empty($combate['b']);

                if (! (($combate['bye'] ?? false) && ($tieneRojo !== $tieneAzul))) {
                    continue;
                }

                $ganador = $combate['a'] ?? $combate['b'] ?? null;

                if (! $ganador) {
                    continue;
                }

                $nextMatch = (int) floor($matchIndex / 2);
                $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

                if (isset($llaves[$roundIndex + 1]['combates'][$nextMatch])
                    && empty($llaves[$roundIndex + 1]['combates'][$nextMatch][$nextSide])) {
                    $llaves[$roundIndex + 1]['combates'][$nextMatch][$nextSide] = $ganador;
                }
            }
        }

        return $llaves;
    }

    private function posicionCombatePorIndice(array $llaves, int $indice): ?array
    {
        $actual = 0;

        foreach ($llaves as $roundIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $matchIndex => $combate) {
                if ($actual === $indice) {
                    return [$roundIndex, $matchIndex];
                }

                $actual++;
            }
        }

        return null;
    }

    private function marcarCombateRealizado(SorteoLlave $sorteo, array $data): void
    {
        $llaves = $sorteo->llaves;
        $roundIndex = (int) $data['round_index'];
        $matchIndex = (int) $data['match_index'];

        if (! isset($llaves[$roundIndex]['combates'][$matchIndex])) {
            return;
        }

        $combate = $llaves[$roundIndex]['combates'][$matchIndex];
        $ganadorSide = ($data['ganador_color'] ?? null) === 'rojo' ? 'a' : 'b';
        $ganadorCompetidor = $combate[$ganadorSide] ?? [
            'nombre' => $data['ganador'] ?? null,
            'organizacion' => '',
            'organizacion_id' => null,
        ];

        $llaves[$roundIndex]['combates'][$matchIndex]['realizado'] = true;
        $llaves[$roundIndex]['combates'][$matchIndex]['ganador'] = [
            'nombre' => $data['ganador'] ?? null,
            'color' => $data['ganador_color'] ?? null,
            'fecha' => Carbon::now()->toDateTimeString(),
        ];

        $nextRound = $roundIndex + 1;
        $nextMatch = (int) floor($matchIndex / 2);
        $nextSide = $matchIndex % 2 === 0 ? 'a' : 'b';

        if (isset($llaves[$nextRound]['combates'][$nextMatch])) {
            $llaves[$nextRound]['combates'][$nextMatch][$nextSide] = $ganadorCompetidor;
        }

        $sorteo->update(['llaves' => $llaves]);
    }

    private function calcularPodio(array $llaves, SorteoLlave $sorteo): array
    {
        $podio = [
            'oro' => '',
            'plata' => '',
            'bronce_1' => '',
            'bronce_2' => '',
        ];

        if (empty($llaves)) {
            return $podio;
        }

        if ($this->esRoundRobin($llaves)) {
            return $this->calcularPodioRoundRobin($llaves, $sorteo, $podio);
        }

        $resultadosPorIndice = $sorteo->resultadosKumite->keyBy('indice_combate');
        $finalRound = count($llaves) - 1;
        $finalIndex = $this->indiceCombatePorPosicion($llaves, $finalRound, 0);
        $finalResultado = $finalIndex !== null ? $resultadosPorIndice->get($finalIndex) : null;

        if ($finalResultado && isset($llaves[$finalRound]['combates'][0])) {
            $final = $llaves[$finalRound]['combates'][0];
            $rojo = $this->nombreCompetidorPodio($final['a'] ?? null);
            $azul = $this->nombreCompetidorPodio($final['b'] ?? null);

            if ($finalResultado->ganador_color === 'rojo') {
                $podio['oro'] = $rojo;
                $podio['plata'] = $azul;
            } else {
                $podio['oro'] = $azul;
                $podio['plata'] = $rojo;
            }
        }

        if (count($llaves) < 2) {
            return $podio;
        }

        $semifinalRound = count($llaves) - 2;
        $bronces = [];

        foreach (($llaves[$semifinalRound]['combates'] ?? []) as $matchIndex => $combate) {
            $indice = $this->indiceCombatePorPosicion($llaves, $semifinalRound, $matchIndex);
            $resultado = $indice !== null ? $resultadosPorIndice->get($indice) : null;

            if (! $resultado) {
                continue;
            }

            $rojo = $this->nombreCompetidorPodio($combate['a'] ?? null);
            $azul = $this->nombreCompetidorPodio($combate['b'] ?? null);
            $perdedor = $resultado->ganador_color === 'rojo' ? $azul : $rojo;

            if ($perdedor) {
                $bronces[] = $perdedor;
            }
        }

        $podio['bronce_1'] = $bronces[0] ?? '';
        $podio['bronce_2'] = $bronces[1] ?? '';

        return $podio;
    }

    private function calcularPodioRoundRobin(array $llaves, SorteoLlave $sorteo, array $podio): array
    {
        $tabla = [];
        $resultadosPorIndice = $sorteo->resultadosKumite->keyBy('indice_combate');
        $indice = 0;

        foreach ($llaves as $ronda) {
            foreach (($ronda['combates'] ?? []) as $combate) {
                $rojo = $this->nombreCompetidorPodio($combate['a'] ?? null);
                $azul = $this->nombreCompetidorPodio($combate['b'] ?? null);

                if ($rojo) {
                    $tabla[$rojo] ??= ['nombre' => $rojo, 'puntos' => 0, 'senshu' => 0, 'victorias' => 0];
                }

                if ($azul) {
                    $tabla[$azul] ??= ['nombre' => $azul, 'puntos' => 0, 'senshu' => 0, 'victorias' => 0];
                }

                $resultado = $resultadosPorIndice->get($indice);

                if ($resultado) {
                    $nombreRojo = $resultado->competidor_rojo ?: $rojo;
                    $nombreAzul = $resultado->competidor_azul ?: $azul;

                    if ($nombreRojo) {
                        $tabla[$nombreRojo] ??= ['nombre' => $nombreRojo, 'puntos' => 0, 'senshu' => 0, 'victorias' => 0];
                        $tabla[$nombreRojo]['puntos'] += (int) $resultado->puntaje_rojo;
                        $tabla[$nombreRojo]['senshu'] += ($resultado->senshu === 'rojo' || $resultado->senshu_rojo) ? 1 : 0;
                        $tabla[$nombreRojo]['victorias'] += $resultado->ganador_color === 'rojo' ? 1 : 0;
                    }

                    if ($nombreAzul) {
                        $tabla[$nombreAzul] ??= ['nombre' => $nombreAzul, 'puntos' => 0, 'senshu' => 0, 'victorias' => 0];
                        $tabla[$nombreAzul]['puntos'] += (int) $resultado->puntaje_azul;
                        $tabla[$nombreAzul]['senshu'] += ($resultado->senshu === 'azul' || $resultado->senshu_azul) ? 1 : 0;
                        $tabla[$nombreAzul]['victorias'] += $resultado->ganador_color === 'azul' ? 1 : 0;
                    }
                }

                $indice++;
            }
        }

        $ordenados = collect($tabla)
            ->sort(function ($a, $b) {
                return [$b['puntos'], $b['senshu'], $b['victorias'], $a['nombre']]
                    <=> [$a['puntos'], $a['senshu'], $a['victorias'], $b['nombre']];
            })
            ->values();

        $podio['oro'] = $ordenados[0]['nombre'] ?? '';
        $podio['plata'] = $ordenados[1]['nombre'] ?? '';
        $podio['bronce_1'] = $ordenados[2]['nombre'] ?? '';

        return $podio;
    }

    private function esRoundRobin(array $llaves): bool
    {
        return ($llaves[0]['sistema'] ?? null) === 'round_robin';
    }

    private function guardarPodioKumite(SorteoLlave $sorteo, array $podio): void
    {
        KumitePodio::updateOrCreate(
            ['sorteo_llave_id' => $sorteo->id],
            [
                'oro' => $podio['oro'] ?: null,
                'plata' => $podio['plata'] ?: null,
                'bronce_1' => $podio['bronce_1'] ?: null,
                'bronce_2' => $podio['bronce_2'] ?: null,
                'generado_at' => Carbon::now(),
            ]
        );
    }

    private function nombreCompetidorPodio(?array $competidor): string
    {
        $nombre = $competidor['nombre'] ?? '';

        return in_array($nombre, ['', 'BYE', 'Competidor', 'Pendiente'], true) || str_starts_with($nombre, 'Ganador')
            ? ''
            : $nombre;
    }

    private function indiceCombatePorPosicion(array $llaves, int $roundIndex, int $matchIndex): ?int
    {
        $indice = 0;

        foreach ($llaves as $rondaIndex => $ronda) {
            foreach (($ronda['combates'] ?? []) as $combateIndex => $combate) {
                if ($rondaIndex === $roundIndex && $combateIndex === $matchIndex) {
                    return $indice;
                }

                $indice++;
            }
        }

        return null;
    }
}
