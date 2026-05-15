<?php

namespace App\Http\Controllers;

use App\Models\EstilosKarate;
use App\Models\Organizacion;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OrganizacionController extends Controller
{
    public function index()
    {
        $personas = Persona::where('status', 1)
            ->orderBy('first_name')
            ->get();

        $estilos = EstilosKarate::where('status', 1)
            ->orderBy('nombre')
            ->get();

        return view('organizaciones.browse', compact('personas', 'estilos'));
    }

    public function ajaxList(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = Organizacion::query()
            ->with(['persona', 'estilo'])
            ->withCount('inscripciones')
            ->withCount('competidores')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('nombre', 'like', "%{$search}%")
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

        $estilos = EstilosKarate::where('status', 1)
            ->orderBy('nombre')
            ->get();

        return response()
            ->view('organizaciones.list', compact('data', 'personas', 'estilos'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function searchPersonas(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        if ($search === '') {
            return response()->json([]);
        }

        $personas = Persona::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('ci', 'like', "%{$search}%");
            })
            ->orderBy('first_name')
            ->limit(20)
            ->get()
            ->map(function ($persona) {
                return [
                    'id' => $persona->id,
                    'text' => $persona->first_name . ($persona->ci ? ' - CI ' . $persona->ci : ''),
                ];
            });

        return response()->json($personas);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:organizaciones,nombre'],
            'estilo_id' => ['nullable', Rule::exists('estiloskarate', 'id')],
            'persona_id' => ['required', Rule::exists('personas', 'id')],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['nullable'],
        ]);

        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('organizaciones', 'public');
        }

        Organizacion::create($data);

        return redirect()
            ->route('organizaciones.index')
            ->with('status', 'Organizacion creada correctamente.');
    }

    public function update(Request $request, Organizacion $organizacion)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('organizaciones', 'nombre')->ignore($organizacion->id),
            ],
            'estilo_id' => ['nullable', Rule::exists('estiloskarate', 'id')],
            'persona_id' => ['required', Rule::exists('personas', 'id')],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['nullable'],
            'editing_organizacion' => ['nullable'],
        ]);
        unset($data['editing_organizacion']);

        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('logo')) {
            if ($organizacion->logo) {
                Storage::disk('public')->delete($organizacion->logo);
            }

            $data['logo'] = $request->file('logo')->store('organizaciones', 'public');
        } else {
            unset($data['logo']);
        }

        $organizacion->update($data);

        return redirect()
            ->route('organizaciones.index')
            ->with('status', 'Organizacion actualizada correctamente.');
    }

    public function toggleStatus(Organizacion $organizacion)
    {
        $organizacion->update([
            'status' => $organizacion->status == 1 ? 0 : 1,
        ]);

        return redirect()
            ->route('organizaciones.index')
            ->with('status', 'Estado de la organizacion actualizado correctamente.');
    }

    public function destroy(Organizacion $organizacion)
    {
        if ($organizacion->inscripciones()->exists()) {
            return redirect()
                ->route('organizaciones.index')
                ->withErrors(['organizacion' => 'No se puede eliminar una organizacion con inscripciones registradas.']);
        }

        if ($organizacion->logo) {
            Storage::disk('public')->delete($organizacion->logo);
        }

        $organizacion->delete();

        return redirect()
            ->route('organizaciones.index')
            ->with('status', 'Organizacion eliminada correctamente.');
    }
}
