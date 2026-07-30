<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sesion;
use App\Models\Actividad;

class SesionFactory extends Factory
{
    protected $model = Sesion::class;

    public function definition()
    {
        $actividad = Actividad::factory()->create();
        $start = $this->faker->dateTimeBetween('+1 days', '+30 days');
        $end = (clone $start)->modify('+90 minutes');

        return [
            'actividad_id' => $actividad->id,
            'numero_sesion' => 1,
            'tema' => $this->faker->sentence(6),
            'start_at' => $start,
            'end_at' => $end,
            'lugar' => $actividad->espacio->nombre ?? 'Sala 1',
            'duracion_minutos' => 90,
            'qr_token' => null,
            'qr_expires_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
