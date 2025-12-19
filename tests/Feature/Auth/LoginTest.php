<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

// Vistas y flujo principal de login

test('la página de login usa el componente de Inertia correcto', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) => $page
        ->component('auth/Login')
        ->has('canRegister')
        ->has('canResetPassword')
        ->has('status')
    );
});

test('un usuario puede autenticarse con credenciales válidas', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect('/dashboard');
});

test('un usuario no puede autenticarse con contraseña inválida', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $response = $this->from(route('login'))->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
});
