<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            CargosSeeder::class,        // Nuevo Seeder añadido para la tabla de cargos
            ProfesionesSeeder::class,
            CarrerasSeeder::class,
            SalonesSeeder::class,
            TipoActividadSeeder::class,
            ModalidadesSeeder::class,
            UsersSeeder::class,         // Crea exclusivamente Rector y Coordinadora
        ]);
    }
}