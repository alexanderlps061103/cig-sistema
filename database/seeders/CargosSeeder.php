<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cargo;

class CargosSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nombre' => 'Rector', 'estado' => true],
            ['nombre' => 'Coordinación', 'estado' => true],
            ['nombre' => 'Docente', 'estado' => true],
        ];

        foreach ($items as $it) {
            Cargo::firstOrCreate(['nombre' => $it['nombre']], $it);
        }
    }
}
