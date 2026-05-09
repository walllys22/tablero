<?php

namespace App\Http\Controllers;

use App\Models\Arbitro;
use App\Models\LicenciaTipo;
use App\Models\Persona;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArbitroController extends Controller
{
    public function index(Torneo $torneo)
    {
        $personas = Persona::where('status', 1)->orderBy('first_name')->get();
        $licencias = LicenciaTipo::orderBy('nombre')->get();
        $arbitros = $torneo->arbitros()
            ->with(['persona', 'licenciaTipo'])
            ->join('personas', 'personas.id', '=', 'arbitros.persona_id')
            ->orderBy('personas.first_name')
            ->select('arbitros.*')
            ->get();

        return view('arbitros.index', compact('torneo', 'personas', 'licencias', 'arbitros'));
    }

    public function store(Request $request, Torneo $torneo)
    {
        $data = $this->validateData($request, $torneo);
        $torneo->arbitros()->create($data);

        return redirect()->route('arbitros.index', $torneo)->with('status', 'Juez registrado correctamente.');
    }

    public function update(Request $request, Torneo $torneo, Arbitro $arbitro)
    {
        abort_unless($arbitro->torneo_id === $torneo->id, 404);

        $data = $this->validateData($request, $torneo, $arbitro);
        $arbitro->update($data);

        return redirect()->route('arbitros.index', $torneo)->with('status', 'Juez actualizado correctamente.');
    }

    public function destroy(Torneo $torneo, Arbitro $arbitro)
    {
        abort_unless($arbitro->torneo_id === $torneo->id, 404);

        $arbitro->delete();

        return redirect()->route('arbitros.index', $torneo)->with('status', 'Juez eliminado correctamente.');
    }

    private function validateData(Request $request, Torneo $torneo, ?Arbitro $arbitro = null): array
    {
        return $request->validate([
            'persona_id' => [
                'required',
                Rule::exists('personas', 'id'),
                Rule::unique('arbitros', 'persona_id')
                    ->where('torneo_id', $torneo->id)
                    ->ignore($arbitro?->id),
            ],
            'cargo' => ['required', 'string', 'in:Juez,Referee'],
            'modalidad' => ['required', 'string', 'in:Kata,Kumite,Kumite-Kata'],
            'rango' => ['required', 'string', 'in:A,B,C'],
            'licencia_tipo_id' => ['required', Rule::exists('licencia_tipos', 'id')],
        ]);
    }
}
