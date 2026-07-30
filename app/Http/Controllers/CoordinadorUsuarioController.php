<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Persona, Estudiante, SolicitudInduccion, UserApproval, Role};
use Illuminate\Support\Facades\DB;

class CoordinadorUsuarioController extends Controller
{
    public function solicitudesRol() {
        // Solicitudes pendientes de Público a Estudiante
        $solicitudes = UserApproval::where('tipo', 'cambio_estudiante')->where('estado', 'pendiente')->get();
        return view('coordinador.usuarios.solicitudes', compact('solicitudes'));
    }

    public function convertirAEstudiante(Request $request, $id) {
        $solicitud = UserApproval::findOrFail($id);

        DB::transaction(function () use ($solicitud, $request) {
            $persona = $solicitud->persona;

            // Cambiar rol en la tabla pivote
            $persona->roles()->updateExistingPivot(5, ['activo' => false]); // Desactivar Público
            $persona->roles()->attach(4, ['activo' => true, 'asignado_por' => auth()->user()->persona_id]); // Activar Estudiante

            // Crear el registro oficial de estudiante
            Estudiante::create([
                'persona_id' => $persona->id,
                'carrera_id' => $request->carrera_id,
                'es_regular' => true
            ]);

            $solicitud->update(['estado' => 'aprobado', 'fecha_respuesta' => now()]);
        });

        return back()->with('success', 'Usuario convertido en Estudiante exitosamente.');
    }

    public function procesarInduccion(Request $request, $id) {
        $sol = SolicitudInduccion::findOrFail($id);

        // Regla: Solo Ciencias Alimentarias
        if ($sol->estudiante->carrera->nombre != 'Ciencias y Culturas Alimentarias') {
            return back()->with('error', 'Esta carrera no posee inducción en este módulo.');
        }

        $sol->update([
            'estado' => $request->estado, // aprobada / rechazada
            'observacion' => $request->observacion,
            'fecha_respuesta' => now()
        ]);

        return back()->with('success', 'Solicitud de inducción procesada.');
    }
}
