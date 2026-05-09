<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Modalidad;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModalidadController extends Controller
{
    public function index(Torneo $torneo)
    {
        $modalidades = $torneo->modalidades()
            ->orderBy('nombre')
            ->orderBy('genero')
            ->get();

        return view('modalidades.browse', compact('torneo', 'modalidades'));
    }

    public function ajaxList(Request $request, Torneo $torneo)
    {
        $search = $request->input('search');
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = $torneo->modalidades()
            ->with(['categorias' => function ($query) {
                $query->orderBy('orden')->orderBy('nombre');
            }])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                        ->orWhere('nombre', 'like', "%{$search}%")
                        ->orWhere('genero', 'like', "%{$search}%")
                        ->orWhereHas('categorias', function ($query) use ($search) {
                            $query->where('nombre', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('modalidades.nombre')
            ->orderBy('modalidades.genero')
            ->select('modalidades.*')
            ->paginate($paginate)
            ->withQueryString();

        $modalidades = $torneo->modalidades()
            ->orderBy('nombre')
            ->orderBy('genero')
            ->get();

        return view('modalidades.list', compact('data', 'torneo', 'modalidades'));
    }

    public function storeCategoria(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'modalidad_id' => [
                'required',
                Rule::exists('modalidades', 'id')->where('torneo_id', $torneo->id),
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'in:Masculino,Femenino,Mixto'],
            'edad_desde' => ['nullable', 'integer', 'min:0', 'max:99'],
            'edad_hasta' => ['nullable', 'integer', 'min:0', 'max:99', 'gte:edad_desde'],
            'peso_desde' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'peso_hasta' => ['nullable', 'numeric', 'min:0', 'max:999.99', 'gte:peso_desde'],
            'grado' => ['nullable', 'string', 'max:100'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'creating_categoria' => ['nullable'],
        ]);
        unset($data['creating_categoria']);
        $data['orden'] = $data['orden'] ?? 0;

        $torneo->categorias()->create($data);

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Categoria creada correctamente.');
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
