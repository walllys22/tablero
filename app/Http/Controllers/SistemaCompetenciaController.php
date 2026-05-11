<?php

namespace App\Http\Controllers;

use App\Models\SistemaCompetencia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SistemaCompetenciaController extends Controller
{
    public function index()
    {
        return view('sistema_competencia.browse');
    }

    public function ajaxList(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = SistemaCompetencia::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('estado', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate($paginate)
            ->withQueryString();

        return response()
            ->view('sistema_competencia.list', compact('data'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:sistema_competencia,nombre'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ]);

        SistemaCompetencia::create($data);

        return redirect()
            ->route('sistema-competencia.index')
            ->with('status', 'Sistema de competencia creado correctamente.');
    }

    public function update(Request $request, SistemaCompetencia $sistemaCompetencia)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sistema_competencia', 'nombre')->ignore($sistemaCompetencia->id),
            ],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'editing_sistema_competencia' => ['nullable'],
        ]);
        unset($data['editing_sistema_competencia']);

        $sistemaCompetencia->update($data);

        return redirect()
            ->route('sistema-competencia.index')
            ->with('status', 'Sistema de competencia actualizado correctamente.');
    }

    public function toggleStatus(SistemaCompetencia $sistemaCompetencia)
    {
        $sistemaCompetencia->update([
            'estado' => $sistemaCompetencia->estado === 'Activo' ? 'Inactivo' : 'Activo',
        ]);

        return redirect()
            ->route('sistema-competencia.index')
            ->with('status', 'Estado del sistema de competencia actualizado correctamente.');
    }

    public function destroy(SistemaCompetencia $sistemaCompetencia)
    {
        if ($sistemaCompetencia->katas()->exists()) {
            return redirect()
                ->route('sistema-competencia.index')
                ->withErrors(['sistema_competencia' => 'No se puede eliminar un sistema de competencia usado por katas.']);
        }

        $sistemaCompetencia->delete();

        return redirect()
            ->route('sistema-competencia.index')
            ->with('status', 'Sistema de competencia eliminado correctamente.');
    }
}
