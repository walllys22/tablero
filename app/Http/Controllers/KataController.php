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
        $sistemas = SistemaCompetencia::where('estado', 'Activo')
            ->orderBy('nombre')
            ->get();

        return view('katas.browse', compact('sistemas'));
    }

    public function ajaxList(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = Kata::query()
            ->with('sistema')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('estado', 'like', "%{$search}%")
                    ->orWhereHas('sistema', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    });
            })
            ->orderBy('nombre')
            ->paginate($paginate)
            ->withQueryString();

        $sistemas = SistemaCompetencia::where('estado', 'Activo')
            ->orderBy('nombre')
            ->get();

        return response()
            ->view('katas.list', compact('data', 'sistemas'))
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
