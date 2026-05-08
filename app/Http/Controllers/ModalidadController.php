<?php

namespace App\Http\Controllers;

use App\Models\Modalidad;
use App\Models\Torneo;
use Illuminate\Http\Request;

class ModalidadController extends Controller
{
    public function index(Torneo $torneo)
    {
        return view('modalidades.browse', compact('torneo'));
    }

    public function ajaxList(Request $request, Torneo $torneo)
    {
        $search = $request->input('search');
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = $torneo->modalidades()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                        ->orWhere('nombre', 'like', "%{$search}%")
                        ->orWhere('genero', 'like', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate($paginate)
            ->withQueryString();

        return view('modalidades.list', compact('data', 'torneo'));
    }

    public function store(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'genero' => ['required', 'string', 'in:Masculino,Femenino'],
            'creating_modalidad' => ['nullable'],
        ]);
        unset($data['creating_modalidad']);

        $torneo->modalidades()->create($data);

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Modalidad creada correctamente.');
    }

    public function update(Request $request, Torneo $torneo, Modalidad $modalidad)
    {
        abort_unless($modalidad->torneo_id === $torneo->id, 404);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'genero' => ['required', 'string', 'in:Masculino,Femenino'],
        ]);

        $modalidad->update($data);

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Modalidad actualizada correctamente.');
    }

    public function destroy(Request $request, Torneo $torneo, Modalidad $modalidad)
    {
        abort_unless($modalidad->torneo_id === $torneo->id, 404);

        $modalidad->delete();

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Modalidad eliminada correctamente.');
    }

}
