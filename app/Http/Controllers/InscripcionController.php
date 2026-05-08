<?php

namespace App\Http\Controllers;

use App\Models\InscripcionCompetidor;
use App\Models\InscripcionOrganizacion;
use App\Models\Modalidad;
use App\Models\Organizacion;
use App\Models\Persona;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InscripcionController extends Controller
{
    public function index(Torneo $torneo)
    {
        $torneo->load('persona');
        $personas = Persona::where('status', 1)->orderBy('first_name')->get();
        $organizaciones = Organizacion::with('persona')
            ->where('status', 1)
            ->orderBy('nombre')
            ->get();
        $modalidades = $torneo->modalidades()
            ->with('categoria')
            ->leftJoin('categorias', 'categorias.id', '=', 'modalidades.categoria_id')
            ->orderBy('categorias.orden')
            ->orderBy('categorias.nombre')
            ->orderBy('modalidades.nombre')
            ->select('modalidades.*')
            ->get();
        $organizacionesInscritas = $torneo->inscripcionOrganizaciones()
            ->with('organizacion.persona')
            ->join('organizaciones', 'organizaciones.id', '=', 'inscripcion_organizaciones.organizacion_id')
            ->orderBy('organizaciones.nombre')
            ->select('inscripcion_organizaciones.*')
            ->get();

        return view('inscripciones.browse', compact('torneo', 'personas', 'organizaciones', 'modalidades', 'organizacionesInscritas'));
    }

    public function ajaxList(Request $request, Torneo $torneo)
    {
        $torneo->load('persona');
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;
        $search = trim((string) $request->input('search', ''));

        $organizaciones = $torneo->inscripcionOrganizaciones()
            ->with(['organizacion.persona', 'competidores.persona', 'competidores.modalidades.modalidad.categoria'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('organizacion', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    })->orWhereHas('competidores.persona', function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('ci', 'like', "%{$search}%");
                    });
                });
            })
            ->orderByDesc('id')
            ->paginate($paginate)
            ->withQueryString();

        return view('inscripciones.list', compact('organizaciones', 'torneo'));
    }

    public function storeOrganizacion(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'organizacion_id' => ['required', Rule::exists('organizaciones', 'id')],
            'costo' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $exists = InscripcionOrganizacion::where('torneo_id', $torneo->id)
            ->where('organizacion_id', $data['organizacion_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['organizacion_id' => 'La organizacion ya esta inscrita en este campeonato.'])
                ->withInput(['creating_organizacion' => 1] + $data);
        }

        $inscripcion = $torneo->inscripcionOrganizaciones()->create([
            'organizacion_id' => $data['organizacion_id'],
            'costo' => $data['costo'],
        ]);

        return redirect()
            ->route('inscripciones.index', $torneo)
            ->with('status', 'Organizacion inscrita correctamente.')
            ->with('recibo_inscripcion_id', $inscripcion->id);
    }

    public function storeCompetidor(Request $request, Torneo $torneo)
    {
        $data = $request->validate([
            'inscripcion_organizacion_id' => [
                'required',
                Rule::exists('inscripcion_organizaciones', 'id')->where('torneo_id', $torneo->id),
            ],
            'persona_id' => ['required', Rule::exists('personas', 'id')],
            'modalidades' => ['required', 'array', 'min:1'],
            'modalidades.*.id' => [
                'required',
                'distinct',
                Rule::exists('modalidades', 'id')->where('torneo_id', $torneo->id),
            ],
            'modalidades.*.costo' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $alreadyRegistered = InscripcionCompetidor::where('torneo_id', $torneo->id)
            ->where('persona_id', $data['persona_id'])
            ->exists();

        if ($alreadyRegistered) {
            return back()
                ->withErrors(['persona_id' => 'Este competidor ya esta inscrito en este campeonato.'])
                ->withInput(['creating_competidor' => 1] + $request->all());
        }

        DB::transaction(function () use ($data, $torneo) {
            $competidor = InscripcionCompetidor::create([
                'torneo_id' => $torneo->id,
                'inscripcion_organizacion_id' => $data['inscripcion_organizacion_id'],
                'persona_id' => $data['persona_id'],
            ]);

            foreach ($data['modalidades'] as $modalidad) {
                $competidor->modalidades()->create([
                    'modalidad_id' => $modalidad['id'],
                    'costo' => $modalidad['costo'],
                ]);
            }
        });

        return redirect()
            ->route('inscripciones.index', $torneo)
            ->with('status', 'Competidor inscrito correctamente.');
    }
}
