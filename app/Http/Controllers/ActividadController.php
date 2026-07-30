<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    /**
     * Almacenar una nueva actividad sin exigir los campos retirados
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'                   => 'required|string|max:255',
            'descripcion'              => 'nullable|string',
            'fecha_inscripcion_inicio' => 'required|date',
            'fecha_inscripcion_fin'    => 'required|date|before_or_equal:fecha',
            'fecha'                    => 'required|date',
            'hora_inicio'              => 'required',
            'hora_fin'                 => 'required|after:hora_inicio',
            'estado'                   => 'required|string|in:pendiente,activa,culminada',
            'id_modalidad'             => 'required|exists:modalidades,id_modalidad',
            'id_salon'                 => 'required|exists:salones,id_salon',
            'id_tipo_actividad'        => 'required|exists:tipo_actividades,id_tipo_actividad',
            'id_trimestre'             => 'required|exists:trimestres,id_trimestre',
            'id_organizador'           => 'nullable|exists:personas,id' // <-- Validar organizador opcional
        ], [
            'fecha_inscripcion_fin.before_or_equal' => 'El fin de la inscripción no puede superar el día de la actividad.',
            'hora_fin.after' => 'La hora de finalización debe ser posterior a la hora de inicio.'
        ]);

        // Fallbacks para campos requeridos por base de datos
        $data['id_tipo_documento'] = 1; 
        $data['id_tema'] = null;           

        Actividad::create($data);
        return redirect()->back()->with('success', 'Actividad registrada de forma exitosa.');
    }

    /**
     * Actualizar una actividad existente
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre'                   => 'required|string|max:255',
            'descripcion'              => 'nullable|string',
            'fecha_inscripcion_inicio' => 'required|date',
            'fecha_inscripcion_fin'    => 'required|date|before_or_equal:fecha',
            'fecha'                    => 'required|date',
            'hora_inicio'              => 'required',
            'hora_fin'                 => 'required|after:hora_inicio',
            'estado'                   => 'required|string|in:pendiente,activa,culminada',
            'id_modalidad'             => 'required|exists:modalidades,id_modalidad',
            'id_salon'                 => 'required|exists:salones,id_salon',
            'id_tipo_actividad'        => 'required|exists:tipo_actividades,id_tipo_actividad',
            'id_trimestre'             => 'required|exists:trimestres,id_trimestre',
            'id_organizador'           => 'nullable|exists:personas,id' // <-- Validar organizador opcional
        ], [
            'fecha_inscripcion_fin.before_or_equal' => 'El fin de la inscripción no puede superar el día de la actividad.',
            'hora_fin.after' => 'La hora de finalización debe ser posterior a la hora de inicio.'
        ]);

        $actividad = Actividad::findOrFail($id);
        $actividad->update($data);

        return redirect()->back()->with('success', 'Actividad actualizada de forma exitosa.');
    }

    /**
     * Eliminar el registro del sistema
     */
    public function destroy($id)
    {
        $actividad = Actividad::findOrFail($id);
        $actividad->delete();
        return redirect()->back()->with('success', 'Actividad eliminada de forma exitosa.');
    }
}