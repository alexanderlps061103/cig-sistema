<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profesion;

class ProfesionesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nombre' => 'Chef de Cuisine', 'descripcion' => 'Profesional experto en alta cocina y dirección gastronómica', 'estado' => true],
            ['nombre' => 'Sommelier', 'descripcion' => 'Especialista en maridajes, vinos y servicio de bebidas', 'estado' => true],
            ['nombre' => 'Pastelero', 'descripcion' => 'Especialista en repostería fina, chocolatería y panificación', 'estado' => true],
            ['nombre' => 'Nutricionista', 'descripcion' => 'Especialista en nutrición humana y dietética alimentaria', 'estado' => true],
        ];

        foreach ($items as $it) {
            Profesion::firstOrCreate(['nombre' => $it['nombre']], $it);
        }
    }
}