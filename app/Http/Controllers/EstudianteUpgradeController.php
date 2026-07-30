<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\ExpedienteEstudiante;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstudianteUpgradeController extends Controller
{
    public function upgrade(Request $request)
    {
        $request->validate([
            'carrera_id' => 'required',
            'documento_pdf' => 'required|mimes:pdf|max:5120',
            'foto_carnet' => 'required|image',
        ]);

        $persona = Auth::user()->persona;

        DB::beginTransaction();
        try {
            // 1. Crear registro en Estudiante
            $estudiante = Estudiante::create([
                'persona_id' => $persona->id,
                'carrera_id' => $request->carrera_id,
                'es_regular' => true,
            ]);

            // 2. Crear Expediente y guardar documentos
            $pathCarnet = $request->file('foto_carnet')->store('expedientes/carnets', 'public');
            $expediente = ExpedienteEstudiante::create([
                'estudiante_id' => $estudiante->id,
                'ruta_carnet' => $pathCarnet,
            ]);

            // 3. Cambiar Rol de 'publico' a 'estudiante'
            $rolPublico = Role::where('nombre', 'publico')->first();
            $rolEstudiante = Role::where('nombre', 'estudiante')->first();

            $persona->roles()->detach($rolPublico->id);
            $persona->roles()->attach($rolEstudiante->id, ['activo' => true, 'asignado_en' => now()]);

            // 4. Actualizar sesión
            session(['role_activo' => 'estudiante']);

            DB::commit();
            return redirect()->route('estudiante.index')->with('success', '¡Ahora eres estudiante regular!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar documentos.');
        }
    }
}
