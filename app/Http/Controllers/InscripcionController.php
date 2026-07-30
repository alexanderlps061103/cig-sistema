<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    // Lógica transaccional pura
    public function registrarInscripcion(Request $request)
    {
        $data = $request->validate([
            'id_actividad' => 'required|exists:actividades,id_actividad',
            'id_estudiante' => 'nullable|integer',
            'id_publico_general' => 'nullable|integer'
        ]);

        $data['fecha_registro'] = now()->toDateString();
        
        $inscripcion = Inscripcion::create($data);
        return response()->json(['success' => true, 'message' => 'Inscripción procesada correctamente.', 'data' => $inscripcion]);
    }

    public function cancelarInscripcion($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $inscripcion->delete();

        return response()->json(['success' => true, 'message' => 'Inscripción cancelada de forma satisfactoria.']);
    }
}
