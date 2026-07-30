<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modalidad;

class ModalidadesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nombre_modalidad' => 'Presencial', 'estado' => true],
            ['nombre_modalidad' => 'Virtual', 'estado' => true],
            ['nombre_modalidad' => 'Mixto', 'estado' => true],
        ];

        foreach ($items as $it) {
            // Buscamos o creamos usando la columna nombre_modalidad
            Modalidad::firstOrCreate(['nombre_modalidad' => $it['nombre_modalidad']], $it);
        }
    }
}