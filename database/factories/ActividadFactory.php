<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Actividad;
use App\Models\Trimestre;
use App\Models\TipoActividad;
use App\Models\Modalidad;
use App\Models\Espacio;
use App\Models\Persona;

class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    public function definition()
    {
        $trimestre = Trimestre::factory()->create();
        $tipo = TipoActividad::factory()->create();
        $modalidad = Modalidad::factory()->create();
        $espacio = Espacio::factory()->create();
        $creador = Persona::factory()->create();

        return [
            'trimestre_id' => $trimestre->id,
            'tipo_actividad_id' => $tipo->id,
            'modalidad_id' => $modalidad->id,
            'nombre' => $this->faker->sentence(4),
            'descripcion' => $this->faker->paragraph(),
            'fecha_inicio_inscripcion' => now()->subDays(10),
            'fecha_fin_inscripcion' => now()->addDays(10),
            'fecha_actividad' => now()->addDays(15)->toDateString(),
            'duracion_total_minutos' => $this->faker->numberBetween(60,180),
            'cupos' => $this->faker->numberBetween(10,50),
            'espacio_id' => $espacio->id,
            'estado' => 'planificada',
            'qr_asistencia' => null,
            'creado_por' => $creador->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
