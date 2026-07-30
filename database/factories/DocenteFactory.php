<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Docente;
use App\Models\Persona;
use App\Models\Profesion;

class DocenteFactory extends Factory
{
    protected $model = Docente::class;

    public function definition()
    {
        $persona = Persona::factory()->create();
        $profesion = Profesion::factory()->create();

        return [
            'persona_id' => $persona->id,
            'profesion_id' => $profesion->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
