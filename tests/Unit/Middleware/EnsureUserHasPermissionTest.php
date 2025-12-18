<?php

use App\Http\Middleware\EnsureUserHasPermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

it('niega acceso si el usuario no está autenticado', function () {
    $middleware = new EnsureUserHasPermission();

    $request = Request::create('/admin/permissions', 'GET');

    $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

    $middleware->handle($request, fn () => new Response(), 'permissions.view');
});

it('permite acceso al superadmin sin validar permiso específico', function () {
    $middleware = new EnsureUserHasPermission();

    $user = User::factory()->withoutTwoFactor()->create();
    $role = Role::factory()->superadmin()->create();
    $user->update(['role_id' => $role->id]);

    $request = Request::create('/admin/permissions', 'GET');
    $request->setUserResolver(fn () => $user);

    $nextCalled = false;

    $middleware->handle($request, function ($req) use (&$nextCalled) {
        $nextCalled = true;

        return new Response();
    }, 'permissions.view');

    expect($nextCalled)->toBeTrue();
});

it('permite acceso si el usuario tiene el permiso requerido', function () {
    $middleware = new EnsureUserHasPermission();

    $user = User::factory()->withoutTwoFactor()->create();
    $role = Role::factory()->create();
    $permission = Permission::factory()->permissionsView()->create();

    $role->permissions()->attach($permission);
    $user->update(['role_id' => $role->id]);

    $request = Request::create('/admin/permissions', 'GET');
    $request->setUserResolver(fn () => $user);

    $nextCalled = false;

    $middleware->handle($request, function ($req) use (&$nextCalled) {
        $nextCalled = true;

        return new Response();
    }, 'permissions.view');

    expect($nextCalled)->toBeTrue();
});

it('niega acceso si el usuario no tiene el permiso requerido', function () {
    $middleware = new EnsureUserHasPermission();

    $user = User::factory()->withoutTwoFactor()->create();
    $role = Role::factory()->create();

    $user->update(['role_id' => $role->id]);

    $request = Request::create('/admin/permissions', 'GET');
    $request->setUserResolver(fn () => $user);

    $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

    $middleware->handle($request, fn () => new Response(), 'permissions.view');
});
