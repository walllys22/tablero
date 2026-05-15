<?php

namespace App\Http\Controllers;

use App\Models\SorteoLlave;
use Illuminate\Http\Request;

class TableroController extends Controller
{
    public function kumite(Request $request)
    {
        $sorteo = $this->sorteoKumite($request);
        $combatesKumite = collect($sorteo->llaves[0]['combates'] ?? [])
            ->map(function ($combate) {
                return [
                    'rojo' => $combate['a']['nombre'] ?? '',
                    'azul' => $combate['b']['nombre'] ?? ($combate['bye'] ?? false ? 'BYE' : ''),
                ];
            })
            ->values()
            ->all();

        $primerCombate = $combatesKumite[0] ?? [];

        $combateInicialKumite = [
            'modalidad' => $sorteo?->modalidad?->nombre ?? 'Kumite Individual',
            'categoria' => $sorteo?->categoria?->nombre ?? '',
            'rojo' => $primerCombate['rojo'] ?? '',
            'azul' => $primerCombate['azul'] ?? '',
        ];

        return view('kumite.tablero', compact('combateInicialKumite', 'combatesKumite'));
    }

    public function kata()
    {
        return view('kata.tablero');
    }

    private function sorteoKumite(Request $request): ?SorteoLlave
    {
        if ($request->filled('sorteo_id')) {
            return SorteoLlave::with(['modalidad', 'categoria'])
                ->whereHas('modalidad', function ($query) {
                    $query->where('nombre', 'like', '%Kumite%');
                })
                ->find($request->input('sorteo_id'));
        }

        return SorteoLlave::with(['modalidad', 'categoria'])
            ->whereHas('modalidad', function ($query) {
                $query->where('nombre', 'like', '%Kumite%');
            })
            ->latest()
            ->first();
    }
}
