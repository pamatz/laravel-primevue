<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_superadmin',
    ];

    protected $casts = [
        'is_superadmin' => 'boolean',
    ];


    /**
     * Los permisos asociados a este rol.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Los usuarios que tienen este rol.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
