<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

describe('UserController', function () {
    /**
     * Autenticación y autorización
     */

    test('usuario sin autenticar no puede ver usuarios', function () {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    });

    test('usuario autenticado sin permiso no puede ver usuarios', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertForbidden();
    });

    test('usuario con permiso puede ver listado de usuarios', function () {
        $permission = Permission::factory()->usersView()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->component('admin/users/Index')
                ->has('users.data')
                ->has('roles');
        });
    });

    test('listado de usuarios expone contrato mínimo para el frontend', function () {
        $permission = Permission::factory()->usersView()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        // Creamos algunos usuarios adicionales para poblar el listado
        User::factory()->withoutTwoFactor()->count(3)->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertInertia(function (\Inertia\Testing\AssertableInertia $page) {
            $page->component('admin/users/Index')
                ->has('users.data')
                ->where('users.per_page', 15)
                ->where('users.total', fn ($total) => $total >= 1)
                ->has('users.data.0', function (\Inertia\Testing\AssertableInertia $user) {
                    $user->has('id')->has('name')->has('email')->etc();
                })
                ->has('roles', function (\Inertia\Testing\AssertableInertia $roles) {
                    $roles->each(fn (\Inertia\Testing\AssertableInertia $role) => $role->has('id')->has('name')->etc());
                });
        });
    });

    test('superadmin puede ver listado de usuarios sin permisos específicos', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $role = Role::factory()->superadmin()->create();
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertOk();
    });

    /**
     * Crear usuarios
     */

    test('usuario sin permiso no puede crear usuario', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
        ]);

        $response->assertForbidden();
    });

    test('usuario con permiso puede crear usuario', function () {
        $permission = Permission::factory()->usersCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'name' => 'Nuevo Usuario',
        ]);
    });

    test('crear usuario con rol', function () {
        $userRole = Role::factory()->create();
        $permission = Permission::factory()->usersCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'Usuario con Rol',
            'email' => 'usuario@example.com',
            'password' => 'password123',
            'role_id' => $userRole->id,
        ]);

        $response->assertRedirect();
        $newUser = User::where('email', 'usuario@example.com')->first();
        $this->assertEquals($userRole->id, $newUser->role_id);
    });

    test('crear usuario requiere nombre, email y contraseña', function () {
        $permission = Permission::factory()->usersCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    });

    test('crear usuario con email duplicado falla', function () {
        $permission = Permission::factory()->usersCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'Usuario',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    });

    test('crear usuario con contraseña corta falla', function () {
        $permission = Permission::factory()->usersCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'Usuario',
            'email' => 'test@example.com',
            'password' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    });

    test('crear usuario con rol inválido falla', function () {
        $permission = Permission::factory()->usersCreate()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'Usuario',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role_id' => 99999,
        ]);

        $response->assertSessionHasErrors('role_id');
    });

    /**
     * Actualizar usuarios
     */

    test('usuario sin permiso no puede actualizar usuario', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $otherUser = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->put(route('admin.users.update', $otherUser), [
            'name' => 'Nombre Actualizado',
            'email' => $otherUser->email,
        ]);

        $response->assertForbidden();
    });

    test('usuario con permiso puede actualizar usuario', function () {
        $updateUser = User::factory()->withoutTwoFactor()->create();
        $permission = Permission::factory()->usersUpdate()->create();
        $authUser = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $authUser->update(['role_id' => $role->id]);

        $response = $this->actingAs($authUser)->put(route('admin.users.update', $updateUser), [
            'name' => 'Nombre Actualizado',
            'email' => $updateUser->email,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $updateUser->id,
            'name' => 'Nombre Actualizado',
        ]);
    });

    test('actualizar usuario con nuevo email', function () {
        $updateUser = User::factory()->withoutTwoFactor()->create();
        $permission = Permission::factory()->usersUpdate()->create();
        $authUser = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $authUser->update(['role_id' => $role->id]);

        $response = $this->actingAs($authUser)->put(route('admin.users.update', $updateUser), [
            'name' => $updateUser->name,
            'email' => 'newemail@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $updateUser->id,
            'email' => 'newemail@example.com',
        ]);
    });

    test('actualizar usuario con contraseña nueva', function () {
        $updateUser = User::factory()->withoutTwoFactor()->create();
        $permission = Permission::factory()->usersUpdate()->create();
        $authUser = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $authUser->update(['role_id' => $role->id]);

        $response = $this->actingAs($authUser)->put(route('admin.users.update', $updateUser), [
            'name' => $updateUser->name,
            'email' => $updateUser->email,
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $updatedUser = User::find($updateUser->id);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $updatedUser->password));
    });

    test('actualizar usuario cambia su rol', function () {
        $newRole = Role::factory()->create();
        $updateUser = User::factory()->withoutTwoFactor()->create();
        $permission = Permission::factory()->usersUpdate()->create();
        $authUser = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $authUser->update(['role_id' => $role->id]);

        $response = $this->actingAs($authUser)->put(route('admin.users.update', $updateUser), [
            'name' => $updateUser->name,
            'email' => $updateUser->email,
            'role_id' => $newRole->id,
        ]);

        $response->assertRedirect();
        $updatedUser = User::find($updateUser->id);
        $this->assertEquals($newRole->id, $updatedUser->role_id);
    });

    test('actualizar usuario requiere nombre y email', function () {
        $updateUser = User::factory()->withoutTwoFactor()->create();
        $permission = Permission::factory()->usersUpdate()->create();
        $authUser = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $authUser->update(['role_id' => $role->id]);

        $response = $this->actingAs($authUser)->put(route('admin.users.update', $updateUser), [
            'name' => '',
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    });

    test('actualizar usuario con email duplicado falla', function () {
        $otherUser = User::factory()->withoutTwoFactor()->create(['email' => 'other@example.com']);
        $updateUser = User::factory()->withoutTwoFactor()->create();
        $permission = Permission::factory()->usersUpdate()->create();
        $authUser = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $authUser->update(['role_id' => $role->id]);

        $response = $this->actingAs($authUser)->put(route('admin.users.update', $updateUser), [
            'name' => $updateUser->name,
            'email' => 'other@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    });

    /**
     * Eliminar usuarios
     */

    test('usuario sin permiso no puede eliminar usuario', function () {
        $user = User::factory()->withoutTwoFactor()->create();
        $otherUser = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->delete(route('admin.users.destroy', $otherUser));

        $response->assertForbidden();
    });

    test('usuario con permiso puede eliminar usuario', function () {
        $deleteUser = User::factory()->withoutTwoFactor()->create();
        $permission = Permission::factory()->usersDelete()->create();
        $authUser = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $authUser->update(['role_id' => $role->id]);

        $response = $this->actingAs($authUser)->delete(route('admin.users.destroy', $deleteUser));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $deleteUser->id]);
    });

    /**
     * Relaciones y permisos
     */

    test('usuario tiene relación con su rol', function () {
        $role = Role::factory()->create();
        $user = User::factory()->withoutTwoFactor()->create(['role_id' => $role->id]);

        $this->assertEquals($role->id, $user->role->id);
    });

    test('usuario sin rol no tiene acceso a permisos de rol', function () {
        $user = User::factory()->withoutTwoFactor()->create(['role_id' => null]);

        $this->assertFalse($user->hasPermission('any.permission'));
    });

    test('usuario verifica si tiene un permiso específico', function () {
        $permission = Permission::factory()->usersView()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach($permission);

        $user = User::factory()->withoutTwoFactor()->create(['role_id' => $role->id]);

        $this->assertTrue($user->hasPermission('users.view'));
        $this->assertFalse($user->hasPermission('users.delete'));
    });

    test('usuario verifica si tiene al menos uno de varios permisos', function () {
        $permission1 = Permission::factory()->usersView()->create();
        $permission2 = Permission::factory()->usersCreate()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach([$permission1->id]);

        $user = User::factory()->withoutTwoFactor()->create(['role_id' => $role->id]);

        $this->assertTrue($user->hasAnyPermission(['users.view', 'users.delete']));
        $this->assertFalse($user->hasAnyPermission(['users.create', 'users.delete']));
    });

    /**
     * Paginación y búsqueda
     */

    test('listado de usuarios está paginado', function () {
        User::factory()->count(20)->create();
        $permission = Permission::factory()->usersView()->create();
        $user = User::factory()->withoutTwoFactor()->create();

        $role = Role::factory()->create();
        $role->permissions()->attach($permission);
        $user->update(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertInertia(function ($page) {
            $page->where('users.per_page', 15);
        });
    });
});
