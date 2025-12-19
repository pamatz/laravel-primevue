<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resource = fake()->unique()->word();
        $action = fake()->randomElement(['view', 'create', 'update', 'delete']);

        return [
            'name' => ucfirst($resource) . ' - ' . ucfirst($action),
            'key' => $resource . '.' . $action,
            'group' => ucfirst($resource),
            'description' => 'Permiso para ' . $action . ' ' . $resource,
        ];
    }

    /**
     * Permiso para ver usuarios.
     */
    public function usersView(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ver Usuarios',
            'key' => 'users.view',
            'group' => 'Usuarios',
            'description' => 'Permiso para ver usuarios',
        ]);
    }

    /**
     * Permiso para crear usuarios.
     */
    public function usersCreate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Crear Usuarios',
            'key' => 'users.create',
            'group' => 'Usuarios',
            'description' => 'Permiso para crear usuarios',
        ]);
    }

    /**
     * Permiso para actualizar usuarios.
     */
    public function usersUpdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Actualizar Usuarios',
            'key' => 'users.update',
            'group' => 'Usuarios',
            'description' => 'Permiso para actualizar usuarios',
        ]);
    }

    /**
     * Permiso para eliminar usuarios.
     */
    public function usersDelete(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Eliminar Usuarios',
            'key' => 'users.delete',
            'group' => 'Usuarios',
            'description' => 'Permiso para eliminar usuarios',
        ]);
    }

    /**
     * Permiso para ver roles.
     */
    public function rolesView(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ver Roles',
            'key' => 'roles.view',
            'group' => 'Roles',
            'description' => 'Permiso para ver roles',
        ]);
    }

    /**
     * Permiso para crear roles.
     */
    public function rolesCreate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Crear Roles',
            'key' => 'roles.create',
            'group' => 'Roles',
            'description' => 'Permiso para crear roles',
        ]);
    }

    /**
     * Permiso para actualizar roles.
     */
    public function rolesUpdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Actualizar Roles',
            'key' => 'roles.update',
            'group' => 'Roles',
            'description' => 'Permiso para actualizar roles',
        ]);
    }

    /**
     * Permiso para eliminar roles.
     */
    public function rolesDelete(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Eliminar Roles',
            'key' => 'roles.delete',
            'group' => 'Roles',
            'description' => 'Permiso para eliminar roles',
        ]);
    }

    /**
     * Permiso para ver permisos.
     */
    public function permissionsView(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ver Permisos',
            'key' => 'permissions.view',
            'group' => 'Permisos',
            'description' => 'Permiso para ver permisos',
        ]);
    }

    /**
     * Permiso para crear permisos.
     */
    public function permissionsCreate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Crear Permisos',
            'key' => 'permissions.create',
            'group' => 'Permisos',
            'description' => 'Permiso para crear permisos',
        ]);
    }

    /**
     * Permiso para actualizar permisos.
     */
    public function permissionsUpdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Actualizar Permisos',
            'key' => 'permissions.update',
            'group' => 'Permisos',
            'description' => 'Permiso para actualizar permisos',
        ]);
    }

    /**
     * Permiso para eliminar permisos.
     */
    public function permissionsDelete(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Eliminar Permisos',
            'key' => 'permissions.delete',
            'group' => 'Permisos',
            'description' => 'Permiso para eliminar permisos',
        ]);
    }
}
