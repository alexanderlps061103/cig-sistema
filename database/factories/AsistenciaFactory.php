<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Asistencia;
use App\Models\Sesion;
use App\Models\Persona;

class AsistenciaFactory extends Factory
{
    protected $model = Asistencia::class;

    public function definition()
    {
        $sesion = Sesion::factory()->create();
        $persona = Persona::factory()->create();

        return [
            'sesion_id' => $sesion->id,
            'persona_id' => $persona->id,
            'fecha_hora' => now(),
            'metodo' => 'QR',
            'registrado_por' => null,
            'minutes_attended' => 90,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
