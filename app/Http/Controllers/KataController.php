<?php

namespace App\Http\Controllers;

use App\Models\Kata;
use App\Models\SistemaCompetencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $sistemas = SistemaCompetencia::query()
            ->orderBy('nombre')
            ->get();

        $activeSystemId = (int) $request->input('sistema_id', optional($sistemas->first())->id);

        if (! $sistemas->contains('id', $activeSystemId)) {
            $activeSystemId = optional($sistemas->first())->id;
        }

        $activeSistema = $sistemas->firstWhere('id', $activeSystemId);
        $matchesSistema = $search !== '' && $activeSistema && stripos($activeSistema->nombre, $search) !== false;

        $data = Kata::with('sistema')
            ->where('sistema_id', $activeSystemId)
            ->when($search !== '' && ! $matchesSistema, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    if (is_numeric($search)) {
                        $query->where('id', $search)
                            ->orWhere('numero', $search)
                            ->orWhere('nombre', 'like', "%{$search}%");

                        return;
                    }

                    $query->where('nombre', 'like', "%{$search}%");
                });
            })
            ->orderBy('numero')
            ->orderBy('id')
            ->paginate($paginate)
            ->appends($request->query());

        $katasOrden = Kata::query()
            ->orderBy('sistema_id')
            ->orderBy('numero')
            ->orderBy('id')
            ->get();

        $sistemasDisponibles = SistemaCompetencia::orderBy('nombre')->get();

        return response()
            ->view('katas.list', compact('sistemas', 'activeSystemId', 'data', 'katasOrden', 'sistemasDisponibles'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('katas', 'numero')->where(fn ($query) => $query->where('sistema_id', $request->input('sistema_id'))),
            ],
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('katas', 'nombre')->where(fn ($query) => $query->where('sistema_id', $request->input('sistema_id'))),
            ],
            'sistema_id' => ['required', Rule::exists('sistema_competencia', 'id')],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ], [
            'nombre.unique' => 'El nombre del kata ya existe en este sistema de competencia.',
            'numero.unique' => 'El numero de kata ya existe en este sistema de competencia.',
        ]);

        Kata::create($data);

        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;
        $totalKatasSistema = Kata::where('sistema_id', $data['sistema_id'])->count();
        $lastPage = max(1, (int) ceil($totalKatasSistema / $paginate));

        return redirect()
            ->route('katas.index')
            ->with('status', 'Kata creado correctamente.')
            ->with('katas_active_system_id', $data['sistema_id'])
            ->with('katas_paginate', $paginate)
            ->with('katas_page', $lastPage);
    }

    public function update(Request $request, Kata $kata)
    {
        $data = $request->validate([
            'numero' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('katas', 'numero')
                    ->where(fn ($query) => $query->where('sistema_id', $request->input('sistema_id')))
                    ->ignore($kata->id),
            ],
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('katas', 'nombre')
                    ->where(fn ($query) => $query->where('sistema_id', $request->input('sistema_id')))
                    ->ignore($kata->id),
            ],
            'sistema_id' => ['required', Rule::exists('sistema_competencia', 'id')],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'editing_kata' => ['nullable'],
        ], [
            'nombre.unique' => 'El nombre del kata ya existe en este sistema de competencia.',
            'numero.unique' => 'El numero de kata ya existe en este sistema de competencia.',
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

    public function updateOrden(Request $request)
    {
        $data = $request->validate([
            'sistema_id' => ['required', Rule::exists('sistema_competencia', 'id')],
            'orden' => ['required', 'array', 'min:1'],
            'orden.*' => ['required', 'integer', 'distinct', Rule::exists('katas', 'id')],
        ]);

        $sistemaId = (int) $data['sistema_id'];
        $ids = collect($data['orden'])->map(fn ($id) => (int) $id)->values();
        $katas = Kata::where('sistema_id', $sistemaId)->orderBy('numero')->orderBy('id')->get();
        $idsActuales = $katas->pluck('id')->values();

        if ($ids->count() !== $idsActuales->count() || $ids->diff($idsActuales)->isNotEmpty()) {
            return back()->withErrors(['orden' => 'El orden enviado no corresponde al sistema seleccionado.']);
        }

        DB::transaction(function () use ($ids, $katas) {
            $offsetTemporal = ((int) $katas->max('numero')) + $ids->count() + 1000;

            foreach ($ids as $index => $id) {
                Kata::whereKey($id)->update(['numero' => $offsetTemporal + $index + 1]);
            }

            foreach ($ids as $index => $id) {
                Kata::whereKey($id)->update(['numero' => $index + 1]);
            }
        });

        return redirect()
            ->route('katas.index')
            ->with('status', 'Orden de katas actualizado correctamente.');
    }

    public function destroy(Kata $kata)
    {
        $kata->delete();

        return redirect()
            ->route('katas.index')
            ->with('status', 'Kata eliminado correctamente.');
    }
}
