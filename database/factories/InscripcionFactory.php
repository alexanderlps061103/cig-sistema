<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Inscripcion;
use App\Models\Persona;
use App\Models\Actividad;

class InscripcionFactory extends Factory
{
    protected $model = Inscripcion::class;

    public function definition()
    {
        $persona = Persona::factory()->create();
        $actividad = Actividad::factory()->create();

        return [
            'persona_id' => $persona->id,
            'actividad_id' => $actividad->id,
            'fecha_inscripcion' => now(),
            'estado' => 'inscrito',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
