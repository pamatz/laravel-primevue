<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Listado de roles.
     */
    public function index(Request $request): Response
    {
        $roles = Role::query()
            ->with('permissions')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $permissions = Permission::query()
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Crea un nuevo rol.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_superadmin' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_superadmin' => $data['is_superadmin'] ?? false,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return back()->with('success', 'Rol creado correctamente.');
    }

    /**
     * Actualiza un rol existente.
     */
    public function update(Role $role, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('roles', 'slug')->ignore($role->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'is_superadmin' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? $role->slug,
            'description' => $data['description'] ?? null,
            'is_superadmin' => $data['is_superadmin'] ?? false,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return back()->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Elimina un rol.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return back()->with('success', 'Rol eliminado correctamente.');
    }
}
