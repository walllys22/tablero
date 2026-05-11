<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Modalidad;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ModalidadController extends Controller
{
    private function orderCategorias($query)
    {
        return $query
            ->orderBy('edad_desde')
            ->orderBy('edad_hasta')
            ->orderBy('genero')
            ->orderBy('peso_hasta')
            ->orderBy('nombre');
    }

    public function index(Torneo $torneo)
    {
        $modalidades = $torneo->modalidades()
            ->with(['categorias' => function ($query) {
                $this->orderCategorias($query);
            }])
            ->orderBy('nombre')
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
                $this->orderCategorias($query);
            }])
            ->withCount('categorias')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                        ->orWhere('nombre', 'like', "%{$search}%")
                        ->orWhereHas('categorias', function ($query) use ($search) {
                            $query->where('nombre', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('modalidades.nombre')
            ->select('modalidades.*')
            ->paginate($paginate)
            ->withQueryString();

        $modalidades = $torneo->modalidades()
            ->orderBy('nombre')
            ->get();

        return view('modalidades.list', compact('data', 'torneo', 'modalidades'));
    }

    public function show(Torneo $torneo, Modalidad $modalidad)
    {
        abort_unless($modalidad->torneo_id === $torneo->id, 404);

        $modalidad->load(['categorias' => function ($query) {
            $this->orderCategorias($query);
        }]);

        return view('modalidades.read', compact('torneo', 'modalidad'));
    }

    public function print(Torneo $torneo)
    {
        $modalidades = $torneo->modalidades()
            ->with(['categorias' => function ($query) {
                $this->orderCategorias($query);
            }])
            ->orderBy('nombre')
            ->get();

        return view('modalidades.print', compact('torneo', 'modalidades'));
    }

    public function storeCategoria(Request $request, Torneo $torneo)
    {
        [$data, $modalidad] = $this->categoriaData($request, $torneo);

        $torneo->categorias()->create($data);

        return back()->with('status', 'Categoria creada correctamente.');
    }

    public function updateCategoria(Request $request, Torneo $torneo, Modalidad $modalidad, Categoria $categoria)
    {
        abort_unless($modalidad->torneo_id === $torneo->id, 404);
        abort_unless($categoria->torneo_id === $torneo->id && $categoria->modalidad_id === $modalidad->id, 404);

        [$data] = $this->categoriaData($request, $torneo, $modalidad, $categoria);

        $categoria->update($data);

        return redirect()
            ->route('modalidades.show', ['torneo' => $torneo, 'modalidad' => $modalidad, 'return' => request('return')])
            ->with('status', 'Categoria actualizada correctamente.');
    }

    public function destroyCategoria(Request $request, Torneo $torneo, Modalidad $modalidad, Categoria $categoria)
    {
        abort_unless($modalidad->torneo_id === $torneo->id, 404);
        abort_unless($categoria->torneo_id === $torneo->id && $categoria->modalidad_id === $modalidad->id, 404);

        $categoria->delete();

        return redirect()
            ->route('modalidades.show', ['torneo' => $torneo, 'modalidad' => $modalidad, 'return' => request('return')])
            ->with('status', 'Categoria eliminada correctamente.');
    }

    private function categoriaData(Request $request, Torneo $torneo, ?Modalidad $modalidad = null, ?Categoria $categoria = null): array
    {
        $rules = [
            'modalidad_id' => [
                $modalidad ? 'nullable' : 'required',
                Rule::exists('modalidades', 'id')->where('torneo_id', $torneo->id),
            ],
            'nombre' => ['nullable', 'string', 'max:255'],
            'prefijo' => ['nullable', 'string', 'max:100'],
            'genero' => ['nullable', 'string', 'in:Masculino,Femenino,Mixto'],
            'edad_desde' => ['nullable', 'integer', 'min:0', 'max:99'],
            'edad_hasta' => ['nullable', 'integer', 'min:0', 'max:99', 'gte:edad_desde'],
            'peso_hasta' => ['nullable', 'numeric', 'min:0', 'max:999.999'],
            'peso_tipo' => ['nullable', 'string', 'in:max,min'],
            'creating_categoria' => ['nullable'],
            'editing_categoria' => ['nullable'],
        ];

        $data = $request->validate($rules);
        $pesoTipo = $data['peso_tipo'] ?? 'max';
        unset($data['creating_categoria'], $data['editing_categoria'], $data['peso_tipo'], $data['prefijo']);

        if (! $modalidad) {
            $modalidad = Modalidad::where('torneo_id', $torneo->id)
                ->findOrFail($data['modalidad_id']);
        }

        $data['modalidad_id'] = $modalidad->id;
        $data['torneo_id'] = $torneo->id;

        $isKata = str_contains(mb_strtolower($modalidad->nombre), 'kata');
        $nombreParts = [];

        if ($isKata && ! empty($request->input('prefijo'))) {
            $nombreParts[] = trim($request->input('prefijo'));
        }

        if (! empty($data['edad_desde']) && ! empty($data['edad_hasta'])) {
            $nombreParts[] = "{$data['edad_desde']} a {$data['edad_hasta']} años";
        } elseif (! empty($data['edad_desde'])) {
            $nombreParts[] = "desde {$data['edad_desde']} años";
        } elseif (! empty($data['edad_hasta'])) {
            $nombreParts[] = "hasta {$data['edad_hasta']} años";
        }

        if (! empty($data['genero'])) {
            $nombreParts[] = $data['genero'];
        }

        if ($isKata) {
            $data['peso_hasta'] = null;
        } elseif ($data['peso_hasta'] !== null) {
            $textoPeso = $pesoTipo === 'min' ? 'mayor o igual' : 'menor o igual';
            $peso = number_format((float) $data['peso_hasta'], 3, '.', '');
            $nombreParts[] = "{$textoPeso} a {$peso} kilos";
        }

        $data['nombre'] = trim(implode(' ', $nombreParts));
        $data['nombre'] = str_replace(["a\xC3\x83\xC2\xB1os", 'anos'], 'años', $data['nombre']);

        if ($data['nombre'] === '') {
            throw ValidationException::withMessages([
                'nombre' => 'Seleccione opciones para generar el nombre de la categoria.',
            ]);
        }

        $exists = Categoria::where('modalidad_id', $data['modalidad_id'])
            ->where('nombre', $data['nombre'])
            ->when($categoria, function ($query, $categoria) {
                $query->where('id', '!=', $categoria->id);
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nombre' => 'Esta categoria ya existe para la modalidad seleccionada.',
            ]);
        }

        return [$data, $modalidad];
    }

    public function store(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modalidades', 'nombre')->where('torneo_id', $torneo->id),
            ],
            'creating_modalidad' => ['nullable'],
        ]);
        unset($data['creating_modalidad']);

        $torneo->modalidades()->create($data);

        return back()->with('status', 'Modalidad creada correctamente.');
    }

    public function update(Request $request, Torneo $torneo, Modalidad $modalidad)
    {
        abort_unless($modalidad->torneo_id === $torneo->id, 404);

        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modalidades', 'nombre')
                    ->where('torneo_id', $torneo->id)
                    ->ignore($modalidad->id),
            ],
        ]);

        $modalidad->update($data);

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Modalidad actualizada correctamente.');
    }

    public function destroy(Request $request, Torneo $torneo, Modalidad $modalidad)
    {
        abort_unless($modalidad->torneo_id === $torneo->id, 404);

        if ($modalidad->categorias()->exists()) {
            return redirect()
                ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
                ->with('warning', 'No se puede eliminar la modalidad porque tiene categorias asignadas.');
        }

        $modalidad->delete();

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Modalidad eliminada correctamente.');
    }

}
