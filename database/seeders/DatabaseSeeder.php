<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Usuario de prueba por defecto
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Rol superadministrador
        $superadminRole = Role::firstOrCreate(
            ['slug' => 'superadmin'],
            [
                'name' => 'Superadministrador',
                'description' => 'Usuario con acceso completo al sistema.',
                'is_superadmin' => true,
            ],
        );

        // Permisos base del sistema
        $permissions = [
            // Dashboard
            ['key' => 'dashboard.view', 'name' => 'Ver panel principal', 'group' => 'General'],

            // Usuarios
            ['key' => 'users.view', 'name' => 'Ver usuarios', 'group' => 'Usuarios'],
            ['key' => 'users.create', 'name' => 'Crear usuarios', 'group' => 'Usuarios'],
            ['key' => 'users.update', 'name' => 'Editar usuarios', 'group' => 'Usuarios'],
            ['key' => 'users.delete', 'name' => 'Eliminar usuarios', 'group' => 'Usuarios'],

            // Roles
            ['key' => 'roles.view', 'name' => 'Ver roles', 'group' => 'Roles'],
            ['key' => 'roles.create', 'name' => 'Crear roles', 'group' => 'Roles'],
            ['key' => 'roles.update', 'name' => 'Editar roles', 'group' => 'Roles'],
            ['key' => 'roles.delete', 'name' => 'Eliminar roles', 'group' => 'Roles'],

            // Permisos
            ['key' => 'permissions.view', 'name' => 'Ver permisos', 'group' => 'Permisos'],
            ['key' => 'permissions.create', 'name' => 'Crear permisos', 'group' => 'Permisos'],
            ['key' => 'permissions.update', 'name' => 'Editar permisos', 'group' => 'Permisos'],
            ['key' => 'permissions.delete', 'name' => 'Eliminar permisos', 'group' => 'Permisos'],
        ];

        $permissionIds = [];

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['key' => $permissionData['key']],
                [
                    'name' => $permissionData['name'],
                    'group' => $permissionData['group'],
                    'description' => null,
                ],
            );

            $permissionIds[] = $permission->id;
        }

        // Asignar todos los permisos al rol superadmin
        $superadminRole->permissions()->syncWithoutDetaching($permissionIds);

        // Usuario superadmin por defecto
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Superadministrador',
                'password' => 'password', // Se encripta automáticamente por el cast "hashed"
            ],
        );

        if (! $superadmin->role) {
            $superadmin->role()->associate($superadminRole);
            $superadmin->save();
        }
    }
}
