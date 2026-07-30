<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrera;

class CarrerasSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nombre' => 'Ciencia y Cultura de la Alimentación', 'descripcion' => 'Estudios científicos y culturales de los sistemas alimentarios', 'estado' => true],
            ['nombre' => 'Gastronomía y Técnicas Culinarias', 'descripcion' => 'Formación profesional en cocina internacional', 'estado' => true],
        ];

        foreach ($items as $it) {
            Carrera::firstOrCreate(['nombre' => $it['nombre']], $it);
        }
    }
}