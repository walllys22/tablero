<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTorneoRequest;
use App\Http\Requests\UpdateTorneoRequest;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TorneoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('eventos.browse');
    }

    public function ajaxList(Request $request)
    {
        $search = $request->input('search');
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = Torneo::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                        ->orWhere('nombre', 'like', "%{$search}%")
                        ->orWhere('lugar', 'like', "%{$search}%")
                        ->orWhere('fecha_inicio', 'like', "%{$search}%")
                        ->orWhere('fecha_fin', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($paginate)
            ->withQueryString();

        return view('eventos.list', compact('data'));
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

        Torneo::create($data);

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
            if ($torneo->logo) {
                Storage::disk('public')->delete($torneo->logo);
            }

            $data['logo'] = $request->file('logo')->store('torneos', 'public');
        } else {
            unset($data['logo']);
        }

        $torneo->update($data);

        return redirect()
            ->route('torneos.index')
            ->with('status', 'Torneo actualizado correctamente.');
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
