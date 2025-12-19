<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

describe('RoleController', function () {
    /**
     * Autenticación y autorización
     */

    test('usuario sin autenticar no puede ver roles', function () {
        $response = $this->get(route('admin.roles.index'));

        $response->assertRedirect(route('login'));
    });

    test('usuario autenticado sin permiso no puede ver roles', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->get(route('admin.roles.index'));

        $response->assertForbidden();
    });

    test('usuario con permiso puede ver listado de roles', function () {
        $permission = Permission::factory()->rolesView()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);

        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.roles.index'));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->component('admin/roles/Index')
                ->has('roles.data')
                ->has('permissions');
        });
    });

    test('listado de roles expone contrato mínimo para el frontend', function () {
        $permission = Permission::factory()->rolesView()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $roleWithPermission = Role::factory()->create();
        $roleWithPermission->permissions()->attach($permission);
        $user->update(['role_id' => $roleWithPermission->id]);

        // Creamos algunos roles y permisos adicionales
        Role::factory()->count(3)->create();
        Permission::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('admin.roles.index'));

        $response->assertInertia(function (\Inertia\Testing\AssertableInertia $page) {
            $page->component('admin/roles/Index')
                ->has('roles.data')
                ->where('roles.per_page', 10)
                ->where('roles.total', fn ($total) => $total >= 1)
                ->has('roles.data.0', function (\Inertia\Testing\AssertableInertia $role) {
                    $role->has('id')->has('name')->has('slug')->has('is_superadmin')->etc();
                })
                ->has('permissions', function (\Inertia\Testing\AssertableInertia $permissions) {
                    $permissions->each(fn (\Inertia\Testing\AssertableInertia $permission) => $permission
                        ->has('id')
                        ->has('name')
                        ->has('key')
                        ->etc()
                    );
                });
        });
    });

    test('superadmin puede ver listado de roles sin permisos específicos', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $role = Role::factory()->superadmin()->create();
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.roles.index'));

        $response->assertOk();
    });

    /**
     * Crear roles
     */

    test('usuario sin permiso no puede crear rol', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->post(route('admin.roles.store'), [
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $response->assertForbidden();
    });

    test('usuario con permiso puede crear rol', function () {
        $permission = Permission::factory()->rolesCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.roles.store'), [
            'name' => 'Nuevo Rol',
            'slug' => 'nuevo-rol',
            'description' => 'Descripción del nuevo rol',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'slug' => 'nuevo-rol',
            'name' => 'Nuevo Rol',
        ]);
    });

    test('crear rol genera slug automáticamente si no se proporciona', function () {
        $permission = Permission::factory()->rolesCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.roles.store'), [
            'name' => 'Mi Nuevo Rol',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'slug' => 'mi-nuevo-rol',
        ]);
    });

    test('crear rol requiere nombre', function () {
        $permission = Permission::factory()->rolesCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.roles.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    });

    test('crear rol con slug duplicado falla', function () {
        $permission = Permission::factory()->rolesCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        Role::factory()->create(['slug' => 'duplicate-slug']);

        $response = $this->actingAs($user)->post(route('admin.roles.store'), [
            'name' => 'Rol Duplicado',
            'slug' => 'duplicate-slug',
        ]);

        $response->assertSessionHasErrors('slug');
    });

    test('crear rol y asignar permisos', function () {
        $permission1 = Permission::factory()->usersView()->create();
        $permission2 = Permission::factory()->usersCreate()->create();
        $createRolePermission = Permission::factory()->rolesCreate()->create();

        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($createRolePermission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.roles.store'), [
            'name' => 'Rol con Permisos',
            'permissions' => [$permission1->id, $permission2->id],
        ]);

        $response->assertRedirect();
        $newRole = Role::where('name', 'Rol con Permisos')->first();
        $this->assertCount(2, $newRole->permissions);
        $this->assertTrue($newRole->permissions->contains($permission1));
        $this->assertTrue($newRole->permissions->contains($permission2));
    });

    test('crear rol como superadmin', function () {
        $permission = Permission::factory()->rolesCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.roles.store'), [
            'name' => 'Super Rol',
            'is_superadmin' => true,
        ]);

        $response->assertRedirect();
        $newRole = Role::where('name', 'Super Rol')->first();
        $this->assertTrue((bool) $newRole->is_superadmin);
    });

    /**
     * Actualizar roles
     */

    test('usuario sin permiso no puede actualizar rol', function () {
        $role = Role::factory()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->put(route('admin.roles.update', $role), [
            'name' => 'Rol Actualizado',
        ]);

        $response->assertForbidden();
    });

    test('usuario con permiso puede actualizar rol', function () {
        $role = Role::factory()->create();
        $updatePermission = Permission::factory()->rolesUpdate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $updateRole = Role::factory()->create();
        $updateRole->permissions()->attach($updatePermission);
        $user->update(['role_id' => $updateRole->id]);

        $response = $this->actingAs($user)->put(route('admin.roles.update', $role), [
            'name' => 'Nombre Actualizado',
            'slug' => $role->slug,
            'description' => 'Descripción actualizada',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Nombre Actualizado',
            'description' => 'Descripción actualizada',
        ]);
    });

    test('actualizar rol mantiene slug si no se proporciona', function () {
        $role = Role::factory()->create(['slug' => 'original-slug']);
        $updatePermission = Permission::factory()->rolesUpdate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $updateRole = Role::factory()->create();
        $updateRole->permissions()->attach($updatePermission);
        $user->update(['role_id' => $updateRole->id]);

        $response = $this->actingAs($user)->put(route('admin.roles.update', $role), [
            'name' => 'Nombre Actualizado',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'slug' => 'original-slug',
        ]);
    });

    test('actualizar rol y sincronizar permisos', function () {
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->usersView()->create();
        $permission2 = Permission::factory()->usersCreate()->create();
        $permission3 = Permission::factory()->usersUpdate()->create();

        $role->permissions()->attach([$permission1->id, $permission2->id]);

        $updatePermission = Permission::factory()->rolesUpdate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $updateRole = Role::factory()->create();
        $updateRole->permissions()->attach($updatePermission);
        $user->update(['role_id' => $updateRole->id]);

        $response = $this->actingAs($user)->put(route('admin.roles.update', $role), [
            'name' => $role->name,
            'permissions' => [$permission2->id, $permission3->id],
        ]);

        $response->assertRedirect();
        $role->refresh();
        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->permissions->contains($permission2));
        $this->assertTrue($role->permissions->contains($permission3));
        $this->assertFalse($role->permissions->contains($permission1));
    });

    /**
     * Eliminar roles
     */

    test('usuario sin permiso no puede eliminar rol', function () {
        $role = Role::factory()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->delete(route('admin.roles.destroy', $role));

        $response->assertForbidden();
    });

    test('usuario con permiso puede eliminar rol', function () {
        $role = Role::factory()->create();
        $deletePermission = Permission::factory()->rolesDelete()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $deleteRole = Role::factory()->create();
        $deleteRole->permissions()->attach($deletePermission);
        $user->update(['role_id' => $deleteRole->id]);

        $response = $this->actingAs($user)->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    });

    /**
     * Relaciones con usuarios y permisos
     */

    test('role tiene relación con permisos', function () {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->permissions()->attach($permission);

        $this->assertTrue($role->permissions->contains($permission));
        $this->assertCount(1, $role->permissions);
    });

    test('role tiene relación con usuarios', function () {
        $role = Role::factory()->create();
        $user1 = User::factory()->withoutTwoFactor()->create(['role_id' => $role->id]);
        $user2 = User::factory()->withoutTwoFactor()->create(['role_id' => $role->id]);

        $this->assertCount(2, $role->users);
        $this->assertTrue($role->users->contains($user1));
        $this->assertTrue($role->users->contains($user2));
    });

    /**
     * Validaciones de superadmin
     */

    test('superadmin puede ver todos los roles', function () {
        Role::factory()->count(5)->create();

        $user = User::factory()->withoutTwoFactor()->create();
        $role = Role::factory()->superadmin()->create();
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.roles.index'));

        $response->assertOk();
    });

    test('rol marcado como superadmin tiene acceso total a permisos', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $superRole = Role::factory()->superadmin()->create();
        $user->update(['role_id' => $superRole->id]);

        $this->assertTrue($user->isSuperAdmin());
    });
});
