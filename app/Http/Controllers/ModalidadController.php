<?php

namespace App\Http\Controllers;

use App\Models\Modalidad;
use App\Models\Torneo;
use Illuminate\Http\Request;

class ModalidadController extends Controller
{
    public function index(Torneo $torneo)
    {
        $this->crearModalidadesEjemplo($torneo);

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

    private function crearModalidadesEjemplo(Torneo $torneo): void
    {
        $modalidades = [
            ['nombre' => 'Kumite Individual', 'genero' => 'Masculino'],
            ['nombre' => 'Kumite Equipos', 'genero' => 'Masculino'],
            ['nombre' => 'Kumite Individual', 'genero' => 'Femenino'],
            ['nombre' => 'Kumite Equipos', 'genero' => 'Femenino'],
        ];

        foreach ($modalidades as $modalidad) {
            Modalidad::firstOrCreate([
                'torneo_id' => $torneo->id,
                'nombre' => $modalidad['nombre'],
                'genero' => $modalidad['genero'],
            ]);
        }
    }
}
