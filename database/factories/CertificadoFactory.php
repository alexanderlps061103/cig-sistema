<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Certificado;
use App\Models\Persona;
use Illuminate\Support\Str;

class CertificadoFactory extends Factory
{
    protected $model = Certificado::class;

    public function definition()
    {
        $persona = Persona::factory()->create();

        return [
            'persona_id' => $persona->id,
            'tipo' => 'estudiante_actividad',
            'actividad_id' => null,
            'sesion_id' => null,
            'codigo_verificacion' => (string) Str::uuid(),
            'fecha_emision' => now()->toDateString(),
            'archivo' => null,
            'firma_sello' => null,
            'qr_data' => null,
            'aprobado_por' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
