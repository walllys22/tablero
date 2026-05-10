<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTorneoRequest;
use App\Http\Requests\UpdateTorneoRequest;
use App\Models\Persona;
use App\Models\Torneo;
use App\Support\DefaultTournamentCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TorneoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personas = Persona::where('status', 1)
            ->orderBy('first_name')
            ->get();

        return view('eventos.browse', compact('personas'));
    }

    public function ajaxList(Request $request)
    {
        $search = $request->input('search');
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = Torneo::query()
            ->with('persona')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                        ->orWhere('nombre', 'like', "%{$search}%")
                        ->orWhere('ciudad', 'like', "%{$search}%")
                        ->orWhere('direccion', 'like', "%{$search}%")
                        ->orWhere('sistema_competencia', 'like', "%{$search}%")
                        ->orWhere('organiza', 'like', "%{$search}%")
                        ->orWhere('lugar', 'like', "%{$search}%")
                        ->orWhere('fecha_inicio', 'like', "%{$search}%")
                        ->orWhere('fecha_fin', 'like', "%{$search}%")
                        ->orWhereHas('persona', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('ci', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate($paginate)
            ->withQueryString();

        $personas = Persona::where('status', 1)
            ->orderBy('first_name')
            ->get();

        return response()
            ->view('eventos.list', compact('data', 'personas'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTorneoRequest $request)
    {
        $data = $request->validated();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('torneos', 'public');
        }

        $torneo = Torneo::create($data);
        DefaultTournamentCatalog::seedFor($torneo);

        return redirect()
            ->route('torneos.index')
            ->with('status', 'Torneo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Torneo $torneo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Torneo $torneo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTorneoRequest $request, Torneo $torneo)
    {
        $data = $request->validated();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'torneo-' . $torneo->id . '-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $file->extension();
            $previousLogo = $torneo->logo;
            $data['logo'] = $file->storeAs('torneos', $filename, 'public');

            if ($previousLogo) {
                Storage::disk('public')->delete($previousLogo);
            }
        } else {
            unset($data['logo']);
        }

        $torneo->update($data);

        return redirect()
            ->route('torneos.index')
            ->with('status', 'Torneo actualizado correctamente.');
    }

    public function updateCostos(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'costo_inscripcion_organizacion' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'costo_inscripcion_competidor' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $torneo->update($data);

        return redirect()
            ->route('torneos.index')
            ->with('status', 'Costos de inscripcion actualizados correctamente.');
    }

    public function toggleStatus(Torneo $torneo)
    {
        $torneo->update([
            'status' => $torneo->status == 1 ? 0 : 1,
        ]);

        return redirect()
            ->route('torneos.index')
            ->with('status', 'Estado del torneo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Torneo $torneo)
    {
        $torneo->delete();

        return redirect()
            ->route('torneos.index')
            ->with('status', 'Torneo eliminado correctamente.');
    }
}
