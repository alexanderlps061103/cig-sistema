<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Usuario;
use App\Models\Persona;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        $persona = Persona::factory()->create();

        return [
            'persona_id' => $persona->id,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'verificado' => true,
            'activo' => true,
            'aprobado_por' => null,
            'aprobado_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
