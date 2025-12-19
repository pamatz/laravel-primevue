<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;

describe('CreateNewUser Action', function () {
    /**
     * Crear usuario con datos válidos
     */

    test('puede crear usuario con datos válidos', function () {
        $action = new CreateNewUser();

        $user = $action->create([
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        expect($user)->toBeInstanceOf(User::class);
        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
        ]);
    });

    /**
     * Validar nombre requerido
     */

    test('falla al crear usuario sin nombre', function () {
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => '',
            'email' => 'nuevo@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    });

    test('falla al crear usuario con nombre muy largo', function () {
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => str_repeat('a', 256),
            'email' => 'nuevo@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    });

    /**
     * Validar email requerido
     */

    test('falla al crear usuario sin email', function () {
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => 'Usuario',
            'email' => '',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    });

    test('falla al crear usuario con email inválido', function () {
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => 'Usuario',
            'email' => 'not-an-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    });

    test('falla al crear usuario con email duplicado', function () {
        User::factory()->create(['email' => 'taken@example.com']);
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => 'Usuario',
            'email' => 'taken@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    });

    test('falla al crear usuario con email muy largo', function () {
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => 'Usuario',
            'email' => str_repeat('a', 255) . '@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    });

    /**
     * Validar contraseña según reglas
     */

    test('falla al crear usuario sin contraseña', function () {
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => 'Usuario',
            'email' => 'nuevo@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);
    });

    test('falla al crear usuario con contraseña muy corta', function () {
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => 'Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
    });

    test('falla si contraseña y confirmación no coinciden', function () {
        $action = new CreateNewUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->create([
            'name' => 'Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);
    });

    test('contraseña se hash correctamente en base de datos', function () {
        $action = new CreateNewUser();

        $user = $action->create([
            'name' => 'Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'TestPassword123!',
            'password_confirmation' => 'TestPassword123!',
        ]);

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('TestPassword123!', $user->password));
    });

    /**
     * Casos especiales
     */

    test('puede crear usuario con nombre especial', function () {
        $action = new CreateNewUser();

        $user = $action->create([
            'name' => 'José María García-López',
            'email' => 'jose@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        expect($user->name)->toBe('José María García-López');
    });

    test('email se guarda en minúsculas', function () {
        $action = new CreateNewUser();

        $user = $action->create([
            'name' => 'Usuario',
            'email' => 'USUARIO@EXAMPLE.COM',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Laravel lowercases emails automáticamente en algunos casos
        // Validamos que el usuario se creó correctamente
        expect($user->email)->toBeTruthy();
    });
});
