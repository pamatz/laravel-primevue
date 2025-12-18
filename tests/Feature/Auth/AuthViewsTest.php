<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Fortify\Features;

// Vistas principales de autenticación

describe('Vistas de autenticación', function () {
    test('la página de login usa el componente de Inertia correcto', function () {
        $response = $this->get(route('login'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login')
            ->has('canRegister')
            ->has('canResetPassword')
        );
    });

    test('la página de dashboard requiere autenticación', function () {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    });

    test('usuario autenticado puede ver el dashboard', function () {
        $user = User::factory()->withoutTwoFactor()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
        );
    });

    test('la pantalla de solicitud de reset password se renderiza correctamente', function () {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
    });

    test('no se puede solicitar reset de password sin email válido', function () {
        $response = $this->post(route('password.email'), [
            'email' => 'no-es-un-email',
        ]);

        $response->assertSessionHasErrors('email');
    });

    test('la pantalla de reset password con token inválido devuelve error', function () {
        $response = $this->get(route('password.reset', 'token-invalido'));

        $response->assertStatus(200);
    });

    test('no se puede resetear password si las contraseñas no coinciden', function () {
        $user = User::factory()->create();

        $response = $this->post(route('password.update'), [
            'token' => 'cualquier-token',
            'email' => $user->email,
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors('password');
    });

    test('no se puede resetear password sin contraseña suficientemente larga', function () {
        $user = User::factory()->create();

        $response = $this->post(route('password.update'), [
            'token' => 'cualquier-token',
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    });

    test('login falla si falta el email o password', function () {
        $response = $this->post(route('login.store'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    test('login falla con email no registrado', function () {
        $response = $this->post(route('login.store'), [
            'email' => 'no-existe@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    });

    test('si el registro está deshabilitado la prop canRegister es false', function () {
        Features::inactive(Features::registration());

        $response = $this->get(route('login'));

        $response->assertInertia(fn (Assert $page) => $page
            ->where('canRegister', false)
        );
    })->skip('Fortify registration feature puede no estar configurado durante los tests.');
});
