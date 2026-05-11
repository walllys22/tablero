<?php

namespace App\Http\Controllers;

use App\Models\EstilosKarate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstilosKarateController extends Controller
{
    public function index()
    {
        return view('estiloskarate.browse');
    }

    public function ajaxList(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = EstilosKarate::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate($paginate)
            ->withQueryString();

        return response()
            ->view('estiloskarate.list', compact('data'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:estiloskarate,nombre'],
            'descripcion' => ['nullable', 'string'],
            'status' => ['nullable'],
        ]);

        $data['status'] = $request->has('status') ? 1 : 0;

        EstilosKarate::create($data);

        return redirect()
            ->route('estiloskarate.index')
            ->with('status', 'Estilo Karate creado correctamente.');
    }

    public function update(Request $request, EstilosKarate $estiloskarate)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('estiloskarate', 'nombre')->ignore($estiloskarate->id),
            ],
            'descripcion' => ['nullable', 'string'],
            'status' => ['nullable'],
            'editing_estilo' => ['nullable'],
        ]);
        unset($data['editing_estilo']);

        $data['status'] = $request->has('status') ? 1 : 0;

        $estiloskarate->update($data);

        return redirect()
            ->route('estiloskarate.index')
            ->with('status', 'Estilo Karate actualizado correctamente.');
    }

    public function toggleStatus(EstilosKarate $estiloskarate)
    {
        $estiloskarate->update([
            'status' => $estiloskarate->status == 1 ? 0 : 1,
        ]);

        return redirect()
            ->route('estiloskarate.index')
            ->with('status', 'Estado de Estilo Karate actualizado correctamente.');
    }

    public function destroy(EstilosKarate $estiloskarate)
    {
        $estiloskarate->delete();

        return redirect()
            ->route('estiloskarate.index')
            ->with('status', 'Estilo Karate eliminado correctamente.');
    }
}
