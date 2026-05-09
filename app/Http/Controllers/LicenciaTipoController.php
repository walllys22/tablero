<?php

namespace App\Http\Controllers;

use App\Models\LicenciaTipo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LicenciaTipoController extends Controller
{
    public function index()
    {
        $licencias = LicenciaTipo::withCount('arbitros')->orderBy('nombre')->get();

        return view('licencias.index', compact('licencias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:licencia_tipos,nombre'],
        ]);

        LicenciaTipo::create($data);

        return redirect()->route('licencias.index')->with('status', 'Tipo de licencia creado correctamente.');
    }

    public function update(Request $request, LicenciaTipo $licencia)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('licencia_tipos', 'nombre')->ignore($licencia->id),
            ],
        ]);

        $licencia->update($data);

        return redirect()->route('licencias.index')->with('status', 'Tipo de licencia actualizado correctamente.');
    }

    public function destroy(LicenciaTipo $licencia)
    {
        if ($licencia->arbitros()->exists()) {
            return redirect()
                ->route('licencias.index')
                ->withErrors(['licencia' => 'No se puede eliminar una licencia usada por jueces.']);
        }

        $licencia->delete();

        return redirect()->route('licencias.index')->with('status', 'Tipo de licencia eliminado correctamente.');
    }
}
