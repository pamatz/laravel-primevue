<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\\Database\\Factories\\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Rol asignado al usuario.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Indica si el usuario es superadministrador.
     */
    public function isSuperAdmin(): bool
    {
        $role = $this->role;

        // Consideramos cualquier valor truthy como superadministrador (1, true, '1').
        return (bool) ($role?->is_superadmin ?? false);
    }

    /**
     * Verifica si el usuario tiene un permiso específico.
     */
    public function hasPermission(string $key): bool
    {
        if (! $this->exists) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->role;

        if (! $role) {
            return false;
        }

        return $role->permissions()
            ->where('key', $key)
            ->exists();
    }

    /**
     * Verifica si el usuario tiene al menos uno de los permisos dados.
     *
     * @param  list<string>  $keys
     */
    public function hasAnyPermission(array $keys): bool
    {
        if (! $this->exists) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->role;

        if (! $role || $keys === []) {
            return false;
        }

        $keys = array_values(array_unique($keys));

        return $role->permissions()
            ->whereIn('key', $keys)
            ->exists();
    }
}
