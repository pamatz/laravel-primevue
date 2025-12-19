<?php

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('ResetUserPassword Action', function () {
    /**
     * Resetear contraseña con datos válidos
     */

    test('puede resetear contraseña de usuario con datos válidos', function () {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);
        $action = new ResetUserPassword();

        $action->reset($user, [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    });

    test('contraseña se actualiza correctamente en base de datos', function () {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);
        $action = new ResetUserPassword();

        $action->reset($user, [
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
        $freshUser = User::find($user->id);
        $this->assertTrue(Hash::check('NewPassword456!', $freshUser->password));
    });

    /**
     * Validar contraseña según reglas
     */

    test('falla al resetear contraseña sin nueva contraseña', function () {
        $user = User::factory()->create();
        $action = new ResetUserPassword();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->reset($user, [
            'password' => '',
            'password_confirmation' => '',
        ]);
    });

    test('falla al resetear contraseña muy corta', function () {
        $user = User::factory()->create();
        $action = new ResetUserPassword();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->reset($user, [
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
    });

    test('falla si nueva contraseña y confirmación no coinciden', function () {
        $user = User::factory()->create();
        $action = new ResetUserPassword();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->reset($user, [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);
    });

    test('falla si nueva contraseña no cumple requisitos de seguridad', function () {
        $user = User::factory()->create();
        $action = new ResetUserPassword();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action->reset($user, [
            'password' => '123456', // muy simple
            'password_confirmation' => '123456',
        ]);
    });

    /**
     * Casos especiales
     */

    test('puede resetear contraseña múltiples veces', function () {
        $user = User::factory()->create([
            'password' => Hash::make('initial-password'),
        ]);
        $action = new ResetUserPassword();

        // Primer reset
        $action->reset($user, [
            'password' => 'FirstReset123!',
            'password_confirmation' => 'FirstReset123!',
        ]);
        $user->refresh();
        $this->assertTrue(Hash::check('FirstReset123!', $user->password));

        // Segundo reset
        $action->reset($user, [
            'password' => 'SecondReset456!',
            'password_confirmation' => 'SecondReset456!',
        ]);
        $user->refresh();
        $this->assertTrue(Hash::check('SecondReset456!', $user->password));
    });

    test('contraseña anterior no funciona después del reset', function () {
        $oldPassword = 'OldPassword123!';
        $user = User::factory()->create([
            'password' => Hash::make($oldPassword),
        ]);
        $action = new ResetUserPassword();

        $action->reset($user, [
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $user->refresh();
        $this->assertFalse(Hash::check($oldPassword, $user->password));
    });

    test('puede resetear contraseña con caracteres especiales', function () {
        $user = User::factory()->create();
        $action = new ResetUserPassword();

        $newPassword = 'P@ssw0rd!#%&*()-_=+[]{}|;:"<>,.?/';

        $action->reset($user, [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));
    });

    test('resetea contraseña de usuario sin contraseña previa', function () {
        // La columna password no puede ser null, así que usamos una contraseña dummy
        $user = User::factory()->create([
            'password' => Hash::make('dummy-initial'),
        ]);
        $action = new ResetUserPassword();

        // Ahora sí reseteamos a una nueva contraseña
        $action->reset($user, [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
        $this->assertFalse(Hash::check('dummy-initial', $user->password));
    });
});
