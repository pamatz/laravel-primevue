<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

// NOTA: el menú dinámico se construye en el front a partir de props.navigation.
// Aquí validamos que el backend proteja correctamente las rutas del dashboard
// y del área de administración según los permisos del usuario.

describe('Dashboard y menú dinámico', function () {
    test('visitante es redirigido a login al acceder al dashboard', function () {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    });

    test('usuario autenticado sin verificación de email no puede acceder al dashboard protegido con verified', function () {
        $user = User::factory()->withoutTwoFactor()->unverified()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('verification.notice'));
    });

    test('usuario autenticado verificado puede acceder al dashboard', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
        );
    });

    test('usuario sin permiso no puede acceder a la vista de usuarios del admin', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertForbidden();
    });

    test('usuario con permiso users.view puede acceder a la sección de usuarios', function () {
        $permission = Permission::factory()->usersView()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach($permission);

        $user = User::factory()->withoutTwoFactor()->create([
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/Index')
        );
    });

    test('usuario con permisos de roles y permisos puede acceder a esas secciones', function () {
        $permissions = [
            Permission::factory()->rolesView()->create(),
            Permission::factory()->permissionsView()->create(),
        ];

        $role = Role::factory()->create();
        $role->permissions()->attach(collect($permissions)->pluck('id'));

        $user = User::factory()->withoutTwoFactor()->create([
            'role_id' => $role->id,
        ]);

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page->component('admin/roles/Index'));

        $this->actingAs($user)
            ->get(route('admin.permissions.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page->component('admin/permissions/Index'));
    });

    test('superadmin puede acceder a todas las secciones del admin', function () {
        $role = Role::factory()->superadmin()->create();
        $user = User::factory()->withoutTwoFactor()->create(['role_id' => $role->id]);

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertStatus(200);

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertStatus(200);

        $this->actingAs($user)
            ->get(route('admin.permissions.index'))
            ->assertStatus(200);
    });
});
