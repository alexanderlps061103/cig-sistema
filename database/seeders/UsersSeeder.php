<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Docente;
use App\Models\Empleado;
use App\Models\Cargo;
use App\Models\Profesion;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        // Obten los roles por nombre (RolesSeeder ya debe haberse ejecutado)
        $rolesMap = Role::pluck('id', 'nombre')->toArray();

        // Obtener los cargos para vincular a los empleados administrativos
        $cargoRector = Cargo::where('nombre', 'Rector')->first();
        $cargoCoord = Cargo::where('nombre', 'Coordinación')->first();

        // Obtener profesión por defecto si aplica para el docente académico
        $profesionDefault = Profesion::first();

        /**
         * Helper para crear o recuperar persona+usuario por email.
         * Devuelve array [$persona, $usuario]
         */
        $getOrCreateUser = function (array $personaData, string $email, string $password = 'password') use ($now) {
            $usuario = Usuario::where('email', $email)->first();
            if ($usuario) {
                return [$usuario->persona, $usuario];
            }

            $persona = Persona::create(array_merge($personaData, [
                'verified_at' => $personaData['verified_at'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]));

            $usuario = Usuario::create([
                'persona_id' => $persona->id,
                'email' => $email,
                'password' => Hash::make($password),
                'verificado' => $personaData['verified_at'] ? true : false,
                'activo' => true,
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return [$persona, $usuario];
        };

        DB::beginTransaction();
        try {
            // 1) RECTOR (Acceso por defecto)
            [$rectorPersona, $rectorUsuario] = $getOrCreateUser([
                'nombres' => 'Rector',
                'apellidos' => 'Institucional',
                'cedula' => 'V10000001',
                'telefono' => '04120000001',
                'sexo' => 'Otro',
                'verified_at' => $now,
            ], 'rector@cig.local', 'password'); // Contraseña: password

            if (isset($rolesMap['rector'])) {
                $rectorPersona->roles()->syncWithoutDetaching([
                    $rolesMap['rector'] => [
                        'asignado_por' => $rectorPersona->id,
                        'activo' => true,
                        'asignado_en' => $now,
                    ]
                ]);
            }

            // Guardar perfil de docente y empleado para el Rector (Dualidad académica/administrativa)
            if ($profesionDefault) {
                Docente::updateOrCreate(['persona_id' => $rectorPersona->id], [
                    'profesion_id' => $profesionDefault->id
                ]);
            }
            if ($cargoRector) {
                Empleado::updateOrCreate(['persona_id' => $rectorPersona->id], [
                    'cargo_id' => $cargoRector->id
                ]);
            }

            // 2) COORDINADORA (Acceso por defecto)
            [$coordPersona, $coordUsuario] = $getOrCreateUser([
                'nombres' => 'Coordinadora',
                'apellidos' => 'Centro',
                'cedula' => 'V20000002',
                'telefono' => '04120000002',
                'sexo' => 'F',
                'verified_at' => $now,
            ], 'coordinador@cig.local', 'password'); // Contraseña: password

            if (isset($rolesMap['coordinador'])) {
                $coordPersona->roles()->syncWithoutDetaching([
                    $rolesMap['coordinador'] => [
                        'asignado_por' => $rectorPersona->id ?? $coordPersona->id,
                        'activo' => true,
                        'asignado_en' => $now,
                    ]
                ]);
            }

            // La coordinadora también es Docente y Empleada (Doble función en el Centro)
            if ($profesionDefault) {
                Docente::updateOrCreate(['persona_id' => $coordPersona->id], [
                    'profesion_id' => $profesionDefault->id
                ]);
            }
            if ($cargoCoord) {
                Empleado::updateOrCreate(['persona_id' => $coordPersona->id], [
                    'cargo_id' => $cargoCoord->id
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}