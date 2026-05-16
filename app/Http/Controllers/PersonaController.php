<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('people.browse');
    }

    public function ajaxList(Request $request)
    {
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;
        $search = trim((string) $request->input('search', ''));
        $order = $request->input('order') === 'desc' ? 'desc' : 'asc';
        $sort = (string) $request->input('sort', 'first_name');
        $sortColumns = [
            'ci' => 'ci',
            'first_name' => 'first_name',
            'birth_date' => 'birth_date',
            'phone' => 'phone',
            'status' => 'status',
        ];
        $sortColumn = $sortColumns[$sort] ?? 'first_name';

        $data = Persona::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('ci', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('gender', 'like', "%{$search}%")
                        ->orWhere('sangre', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortColumn, $order)
            ->orderBy('first_name', $order)
            ->orderBy('id', $order)
            ->paginate($paginate)
            ->withQueryString();

        return view('people.list', compact('data', 'sort', 'order'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        $data = $request->validated();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('personas', 'public');
        }

        Persona::create($data);

        return redirect()
            ->route('people.browse')
            ->with('status', 'Persona creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Persona $persona)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        $data = $request->validated();
        $data['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($persona->image) {
                Storage::disk('public')->delete($persona->image);
            }

            $data['image'] = $request->file('image')->store('personas', 'public');
        } else {
            unset($data['image']);
        }

        $persona->update($data);

        return redirect()
            ->route('people.browse')
            ->with('status', 'Persona actualizada correctamente.');
    }

    public function toggleStatus(Persona $persona)
    {
        $persona->update([
            'status' => $persona->status == 1 ? 0 : 1,
        ]);

        return redirect()
            ->route('people.browse')
            ->with('status', 'Estado de la persona actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        $persona->delete();

        return redirect()
            ->route('people.browse')
            ->with('status', 'Persona eliminada correctamente.');
    }
}
