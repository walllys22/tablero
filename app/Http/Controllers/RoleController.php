<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $permissionOptions = $this->permissionOptions();

        return view('roles.browse', compact('permissionOptions'));
    }

    public function ajaxList(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = Role::query()
            ->withCount('users')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate($paginate)
            ->withQueryString();

        $permissionOptions = $this->permissionOptions();

        return response()
            ->view('roles.list', compact('data', 'permissionOptions'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys($this->permissionOptions()))],
            'status' => ['nullable'],
        ]);

        $data['permissions'] = array_values($data['permissions'] ?? []);
        $data['status'] = $request->has('status') ? 1 : 0;

        Role::create($data);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Rol creado correctamente.');
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys($this->permissionOptions()))],
            'status' => ['nullable'],
            'editing_role' => ['nullable'],
        ]);
        unset($data['editing_role']);

        $data['permissions'] = array_values($data['permissions'] ?? []);
        $data['status'] = $request->has('status') ? 1 : 0;

        $role->update($data);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Rol actualizado correctamente.');
    }

    public function toggleStatus(Role $role)
    {
        $role->update([
            'status' => $role->status == 1 ? 0 : 1,
        ]);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Estado del rol actualizado correctamente.');
    }

    private function permissionOptions(): array
    {
        return [
            'ver' => 'Ver',
            'crear' => 'Crear',
            'editar' => 'Editar',
            'eliminar' => 'Eliminar',
            'imprimir' => 'Imprimir',
            'activar' => 'Activar/Inactivar',
            'ordenar' => 'Ordenar',
            'sortear' => 'Sortear',
        ];
    }
}
