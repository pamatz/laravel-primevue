<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

describe('PermissionController', function () {
    /**
     * Autenticación y autorización
     */

    test('usuario sin autenticar no puede ver permisos', function () {
        $response = $this->get(route('admin.permissions.index'));

        $response->assertRedirect(route('login'));
    });

    test('usuario autenticado sin permiso no puede ver permisos', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->get(route('admin.permissions.index'));

        $response->assertForbidden();
    });

    test('usuario con permiso puede ver listado de permisos', function () {
        $permission = Permission::factory()->permissionsView()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);

        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.permissions.index'));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->component('admin/permissions/Index')
                ->has('permissions.data');
        });
    });

    test('listado de permisos expone contrato mínimo para el frontend', function () {
        $permission = Permission::factory()->permissionsView()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        Permission::factory()->count(5)->create();

        $response = $this->actingAs($user)->get(route('admin.permissions.index'));

        $response->assertInertia(function (\Inertia\Testing\AssertableInertia $page) {
            $page->component('admin/permissions/Index')
                ->has('permissions.data')
                ->where('permissions.per_page', 15)
                ->where('permissions.total', fn ($total) => $total >= 1)
                ->has('permissions.data.0', function (\Inertia\Testing\AssertableInertia $permission) {
                    $permission->has('id')->has('name')->has('key')->etc();
                });
        });
    });

    test('superadmin puede ver listado de permisos sin permisos específicos', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $role = Role::factory()->superadmin()->create();
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.permissions.index'));

        $response->assertOk();
    });

    /**
     * Crear permisos
     */

    test('usuario sin permiso no puede crear permiso', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->post(route('admin.permissions.store'), [
            'name' => 'Test Permission',
            'key' => 'test.permission',
            'group' => 'Test',
            'description' => 'Test description',
        ]);

        $response->assertForbidden();
    });

    test('usuario con permiso puede crear permiso', function () {
        $permission = Permission::factory()->permissionsCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.permissions.store'), [
            'name' => 'Nueva Permiso',
            'key' => 'nueva.permiso',
            'group' => 'Nueva',
            'description' => 'Descripción del nuevo permiso',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'key' => 'nueva.permiso',
            'name' => 'Nueva Permiso',
        ]);
    });

    test('crear permiso requiere nombre y key', function () {
        $permission = Permission::factory()->permissionsCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.permissions.store'), [
            'name' => '',
            'key' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'key']);
    });

    test('crear permiso con key duplicado falla', function () {
        $permission = Permission::factory()->permissionsCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        Permission::factory()->create(['key' => 'duplicate.key']);

        $response = $this->actingAs($user)->post(route('admin.permissions.store'), [
            'name' => 'Permiso Duplicado',
            'key' => 'duplicate.key',
        ]);

        $response->assertSessionHasErrors('key');
    });

    /**
     * Actualizar permisos
     */

    test('usuario sin permiso no puede actualizar permiso', function () {
        $permission = Permission::factory()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->put(route('admin.permissions.update', $permission), [
            'name' => 'Permiso Actualizado',
            'key' => 'actualizado.key',
        ]);

        $response->assertForbidden();
    });

    test('usuario con permiso puede actualizar permiso', function () {
        $permission = Permission::factory()->create();
        $updatePermission = Permission::factory()->permissionsUpdate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($updatePermission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->put(route('admin.permissions.update', $permission), [
            'name' => 'Nombre Actualizado',
            'key' => $permission->key,
            'group' => 'Grupo Actualizado',
            'description' => 'Descripción actualizada',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'Nombre Actualizado',
            'group' => 'Grupo Actualizado',
        ]);
    });

    test('actualizar permiso requiere nombre y key', function () {
        $permission = Permission::factory()->create();
        $updatePermission = Permission::factory()->permissionsUpdate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($updatePermission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->put(route('admin.permissions.update', $permission), [
            'name' => '',
            'key' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'key']);
    });

    test('actualizar permiso con key duplicado falla', function () {
        $permission = Permission::factory()->create(['key' => 'original.key']);
        $otherPermission = Permission::factory()->create(['key' => 'other.key']);
        $updatePermission = Permission::factory()->permissionsUpdate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($updatePermission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->put(route('admin.permissions.update', $permission), [
            'name' => 'Permiso',
            'key' => 'other.key',
        ]);

        $response->assertSessionHasErrors('key');
    });

    /**
     * Eliminar permisos
     */

    test('usuario sin permiso no puede eliminar permiso', function () {
        $permission = Permission::factory()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->delete(route('admin.permissions.destroy', $permission));

        $response->assertForbidden();
    });

    test('usuario con permiso puede eliminar permiso', function () {
        $permission = Permission::factory()->create();
        $deletePermission = Permission::factory()->permissionsDelete()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($deletePermission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->delete(route('admin.permissions.destroy', $permission));

        $response->assertRedirect();
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    });

    /**
     * Paginación y búsqueda
     */

    test('listado de permisos está paginado', function () {
        Permission::factory()->count(20)->create();
        $permission = Permission::factory()->permissionsView()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.permissions.index'));

        $response->assertInertia(function ($page) {
            $page->where('permissions.per_page', 15);
        });
    });
});
