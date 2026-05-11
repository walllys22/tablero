<?php

namespace App\Http\Controllers;

use App\Models\Kata;
use App\Models\SistemaCompetencia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KataController extends Controller
{
    public function index()
    {
        $sistemas = SistemaCompetencia::orderBy('nombre')->get();

        return view('katas.browse', compact('sistemas'));
    }

    public function ajaxList(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $sistemas = SistemaCompetencia::with(['katas' => function ($query) {
                $query->orderBy('id');
            }])
            ->orderBy('nombre')
            ->get();

        if ($search !== '') {
            $sistemas = $sistemas
                ->map(function ($sistema) use ($search) {
                    $matchesSistema = stripos($sistema->nombre, $search) !== false;

                    if (! $matchesSistema) {
                        $sistema->setRelation('katas', $sistema->katas->filter(function ($kata) use ($search) {
                            return (string) $kata->id === $search
                                || stripos($kata->nombre, $search) !== false;
                        })->values());
                    }

                    return $sistema;
                })
                ->filter(function ($sistema) {
                    return $sistema->katas->isNotEmpty();
                })
                ->values();
        }

        $sistemasDisponibles = SistemaCompetencia::orderBy('nombre')->get();

        return response()
            ->view('katas.list', compact('sistemas', 'sistemasDisponibles'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:katas,nombre'],
            'sistema_id' => ['required', Rule::exists('sistema_competencia', 'id')],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ]);

        Kata::create($data);

        return redirect()
            ->route('katas.index')
            ->with('status', 'Kata creado correctamente.');
    }

    public function update(Request $request, Kata $kata)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('katas', 'nombre')->ignore($kata->id),
            ],
            'sistema_id' => ['required', Rule::exists('sistema_competencia', 'id')],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'editing_kata' => ['nullable'],
        ]);
        unset($data['editing_kata']);

        $kata->update($data);

        return redirect()
            ->route('katas.index')
            ->with('status', 'Kata actualizado correctamente.');
    }

    public function toggleStatus(Kata $kata)
    {
        $kata->update([
            'estado' => $kata->estado === 'Activo' ? 'Inactivo' : 'Activo',
        ]);

        return redirect()
            ->route('katas.index')
            ->with('status', 'Estado del kata actualizado correctamente.');
    }

    public function destroy(Kata $kata)
    {
        $kata->delete();

        return redirect()
            ->route('katas.index')
            ->with('status', 'Kata eliminado correctamente.');
    }
}
