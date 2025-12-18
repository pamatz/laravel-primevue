<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'is_superadmin' => false,
        ];
    }

    /**
     * Indica que el rol es un superadministrador.
     */
    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Superadministrador',
            'slug' => 'superadmin',
            'is_superadmin' => true,
        ]);
    }

    /**
     * Rol de editor.
     */
    public function editor(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Puede editar y crear contenido',
            'is_superadmin' => false,
        ]);
    }

    /**
     * Rol de viewer.
     */
    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Viewer',
            'slug' => 'viewer',
            'description' => 'Puede solo visualizar contenido',
            'is_superadmin' => false,
        ]);
    }
}
