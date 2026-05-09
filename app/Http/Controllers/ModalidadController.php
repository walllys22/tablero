<?php

namespace App\Http\Controllers;

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
                $query->orderBy('nombre');
            }])
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

    public function storeCategoria(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'modalidad_id' => [
                'required',
                Rule::exists('modalidades', 'id')->where('torneo_id', $torneo->id),
            ],
            'nombre' => ['nullable', 'string', 'max:255'],
            'nombre_base' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'in:Masculino,Femenino,Mixto'],
            'edad_desde' => ['nullable', 'integer', 'min:0', 'max:99'],
            'edad_hasta' => ['nullable', 'integer', 'min:0', 'max:99', 'gte:edad_desde'],
            'peso_hasta' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'peso_tipo' => ['nullable', 'string', 'in:max,min'],
            'creating_categoria' => ['nullable'],
        ]);
        $pesoTipo = $data['peso_tipo'] ?? 'max';
        $nombreBase = $data['nombre_base'] ?? $data['nombre'] ?? '';
        unset($data['creating_categoria'], $data['peso_tipo'], $data['nombre_base']);

        $modalidad = Modalidad::where('torneo_id', $torneo->id)
            ->findOrFail($data['modalidad_id']);

        $isKata = str_contains(mb_strtolower($modalidad->nombre), 'kata')
            || str_contains(mb_strtolower($nombreBase), 'kata');

        $baseNombre = trim(preg_replace('/\s+(menor|mayor)\s+o\s+igual\s+a\s+\d+([.,]\d+)?\s+kilos?$/iu', '', $nombreBase));
        $baseNombre = trim(preg_replace('/\s+(masculino|femenino|mixto)$/iu', '', $baseNombre));
        $baseNombre = trim(preg_replace('/\s+(\d+\s+a\s+\d+\s+anos|desde\s+\d+\s+anos|hasta\s+\d+\s+anos)$/iu', '', $baseNombre));
        $nombreParts = [];

        if ($baseNombre !== '') {
            $nombreParts[] = $baseNombre;
        }

        if (! empty($data['edad_desde']) && ! empty($data['edad_hasta'])) {
            $nombreParts[] = "{$data['edad_desde']} a {$data['edad_hasta']} anos";
        } elseif (! empty($data['edad_desde'])) {
            $nombreParts[] = "desde {$data['edad_desde']} anos";
        } elseif (! empty($data['edad_hasta'])) {
            $nombreParts[] = "hasta {$data['edad_hasta']} anos";
        }

        if (! empty($data['genero'])) {
            $nombreParts[] = $data['genero'];
        }

        if ($isKata) {
            $data['peso_hasta'] = null;
        } elseif ($data['peso_hasta'] !== null) {
            $textoPeso = $pesoTipo === 'min' ? 'mayor o igual' : 'menor o igual';
            $peso = rtrim(rtrim((string) $data['peso_hasta'], '0'), '.');
            $nombreParts[] = "{$textoPeso} a {$peso} kilos";
        }

        $data['nombre'] = trim(implode(' ', $nombreParts));

        if ($data['nombre'] === '') {
            return back()
                ->withErrors(['nombre' => 'Seleccione opciones para generar el nombre de la categoria.'])
                ->withInput();
        }

        $torneo->categorias()->create($data);

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Categoria creada correctamente.');
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

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Modalidad creada correctamente.');
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

        $modalidad->delete();

        return redirect()
            ->route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')])
            ->with('status', 'Modalidad eliminada correctamente.');
    }

}
