<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoActividad;

class TipoActividadSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'nombre' => 'Charla', 
                'descripcion' => 'Conferencia breve informativa', 
                'duracion' => '01:30:00', 
                'estado' => true
            ],
            [
                'nombre' => 'Seminario', 
                'descripcion' => 'Sesión académica de aprendizaje profundo', 
                'duracion' => '03:00:00', 
                'estado' => true
            ],
            [
                'nombre' => 'Foro', 
                'descripcion' => 'Espacio de discusión e intercambio de ideas', 
                'duracion' => '02:00:00', 
                'estado' => true
            ],
            [
                'nombre' => 'Taller', 
                'descripcion' => 'Actividad práctica guiada', 
                'duracion' => '04:00:00', 
                'estado' => true
            ],
        ];

        foreach ($items as $it) {
            TipoActividad::firstOrCreate(['nombre' => $it['nombre']], $it);
        }
    }
}