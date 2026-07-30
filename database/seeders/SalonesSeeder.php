<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Salon;

class SalonesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nombre' => 'Cocina de Prácticas Calientes', 'capacidad' => 24, 'estado' => true],
            ['nombre' => 'Taller de Pastelería y Repostería', 'capacidad' => 20, 'estado' => true],
            ['nombre' => 'Laboratorio de Análisis Sensorial (Cata)', 'capacidad' => 15, 'estado' => true],
        ];

        foreach ($items as $it) {
            Salon::firstOrCreate(['nombre' => $it['nombre']], $it);
        }
    }
}