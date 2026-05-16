<?php

namespace App\Http\Controllers;

use App\Models\Competidor;
use App\Models\Organizacion;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class CompetidorController extends Controller
{
    public function index(Organizacion $organizacion)
    {
        $organizacion->load(['persona', 'estilo']);
        $personas = $this->personasDisponibles($organizacion);

        return view('competidores.browse', compact('organizacion', 'personas'));
    }

    public function ajaxList(Request $request, Organizacion $organizacion)
    {
        $search = trim((string) $request->input('search', ''));
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;
        $order = $request->input('order') === 'desc' ? 'desc' : 'asc';

        $data = $organizacion->competidores()
            ->with('persona')
            ->join('personas', 'personas.id', '=', 'competidores.persona_id')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('personas.ci', 'like', "%{$search}%")
                        ->orWhere('personas.first_name', 'like', "%{$search}%")
                        ->orWhere('personas.email', 'like', "%{$search}%")
                        ->orWhere('personas.phone', 'like', "%{$search}%")
                        ->orWhere('personas.gender', 'like', "%{$search}%")
                        ->orWhere('personas.sangre', 'like', "%{$search}%");
                });
            })
            ->orderBy('personas.first_name', $order)
            ->orderBy('competidores.id', $order)
            ->select('competidores.*')
            ->paginate($paginate)
            ->withQueryString();

        return response()
            ->view('competidores.list', compact('data', 'organizacion'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request, Organizacion $organizacion)
    {
        $validator = validator($request->all(), [
            'persona_ids' => ['required', 'array', 'min:1'],
            'persona_ids.*' => [
                'required',
                'distinct',
                Rule::exists('personas', 'id')->where('status', 1),
                Rule::unique('competidores', 'persona_id'),
            ],
            'pesos' => ['nullable', 'array'],
            'pesos.*' => ['nullable', 'numeric', 'min:0', 'max:999.999'],
            'status' => ['nullable'],
        ], [
            'persona_ids.required' => 'Seleccione al menos una persona.',
            'persona_ids.*.unique' => 'Una de las personas seleccionadas ya pertenece a una organizacion.',
        ]);
        $validator->after(function (Validator $validator) use ($request) {
            $responsables = Organizacion::whereIn('persona_id', $request->input('persona_ids', []))
                ->pluck('persona_id');

            if ($responsables->isNotEmpty()) {
                $validator->errors()->add('persona_ids', 'No se puede agregar como competidor a responsables de organizacion.');
            }
        });

        $data = $validator->validate();

        foreach ($data['persona_ids'] as $personaId) {
            $organizacion->competidores()->create([
                'persona_id' => $personaId,
                'peso' => $data['pesos'][$personaId] ?? null,
                'status' => $request->has('status') ? 1 : 0,
            ]);
        }

        return redirect()
            ->route('organizaciones.competidores.index', $organizacion)
            ->with('status', 'Competidores agregados correctamente.');
    }

    public function toggleStatus(Organizacion $organizacion, Competidor $competidor)
    {
        abort_unless($competidor->organizacion_id === $organizacion->id, 404);

        $competidor->update([
            'status' => $competidor->status == 1 ? 0 : 1,
        ]);

        return redirect()
            ->route('organizaciones.competidores.index', $organizacion)
            ->with('status', 'Estado del competidor actualizado correctamente.');
    }

    public function update(Request $request, Organizacion $organizacion, Competidor $competidor)
    {
        abort_unless($competidor->organizacion_id === $organizacion->id, 404);

        $persona = $competidor->persona;

        $data = $request->validate([
            'ci' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('personas', 'ci')->ignore($persona?->id),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'string', 'max:50'],
            'sangre' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('personas', 'email')->ignore($persona?->id),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'peso' => ['nullable', 'numeric', 'min:0', 'max:999.999'],
        ]);

        if ($persona) {
            $persona->update([
                'ci' => $data['ci'] ?? null,
                'first_name' => $data['first_name'],
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'sangre' => $data['sangre'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
            ]);
        }

        $competidor->update([
            'peso' => $data['peso'] ?? null,
        ]);

        return redirect()
            ->route('organizaciones.competidores.index', $organizacion)
            ->with('status', 'Datos del competidor actualizados correctamente.');
    }

    public function destroy(Organizacion $organizacion, Competidor $competidor)
    {
        abort_unless($competidor->organizacion_id === $organizacion->id, 404);

        $competidor->delete();

        return redirect()
            ->route('organizaciones.competidores.index', $organizacion)
            ->with('status', 'Competidor eliminado correctamente.');
    }

    private function personasDisponibles(Organizacion $organizacion)
    {
        return Persona::where('status', 1)
            ->whereDoesntHave('competidores')
            ->whereDoesntHave('organizaciones')
            ->orderBy('first_name')
            ->get();
    }
}
