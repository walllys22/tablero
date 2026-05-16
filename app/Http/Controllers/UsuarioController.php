<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name')->get();

        return view('usuarios.browse', compact('roles'));
    }

    public function ajaxList(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $paginate = (int) $request->input('paginate', 10);
        $paginate = in_array($paginate, [10, 25, 50, 100], true) ? $paginate : 10;

        $data = User::query()
            ->with('roles')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('name')
            ->paginate($paginate)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return response()
            ->view('usuarios.list', compact('data', 'roles'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
            'status' => ['nullable'],
        ]);

        $roleIds = $data['roles'] ?? [];
        unset($data['roles'], $data['password_confirmation']);

        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();
        $data['status'] = $request->has('status') ? 1 : 0;
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('usuarios', 'public');
        }

        $user = User::create($data);
        $user->roles()->sync($roleIds);

        return redirect()
            ->route('usuarios.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
            'status' => ['nullable'],
            'editing_usuario' => ['nullable'],
        ]);

        $roleIds = $data['roles'] ?? [];
        unset($data['roles'], $data['password_confirmation'], $data['editing_usuario']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $data['status'] = $request->has('status') ? 1 : 0;
        if ($request->hasFile('imagen')) {
            if ($usuario->imagen) {
                Storage::disk('public')->delete($usuario->imagen);
            }

            $data['imagen'] = $request->file('imagen')->store('usuarios', 'public');
        }

        $usuario->update($data);
        $usuario->roles()->sync($roleIds);

        return redirect()
            ->route('usuarios.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggleStatus(User $usuario)
    {
        $usuario->update([
            'status' => $usuario->status == 1 ? 0 : 1,
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('status', 'Estado del usuario actualizado correctamente.');
    }
}
