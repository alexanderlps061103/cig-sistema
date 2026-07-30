<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\Carrera;

class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition()
    {
        $persona = Persona::factory()->create();
        $carrera = Carrera::factory()->create();

        return [
            'persona_id' => $persona->id,
            'carrera_id' => $carrera->id,
            'modalidad_egreso' => $this->faker->randomElement(['pasantia','acreditacion']),
            'es_regular' => $this->faker->boolean(70),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
