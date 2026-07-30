<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('roles')->insertOrIgnore([
            ['nombre' => 'rector', 'descripcion' => 'Rector del centro', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'coordinador', 'descripcion' => 'Coordinador de actividades', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'docente', 'descripcion' => 'Docente / Ponente', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'estudiante', 'descripcion' => 'Estudiante UNey', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'publico', 'descripcion' => 'Público general', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
