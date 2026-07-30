<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tema;
use App\Models\Actividad;
use Illuminate\Http\Request;

class TemaController extends Controller
{
    /**
     * Guarda una nueva sesión (Tema) vinculada a una actividad.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_actividad'     => 'required|exists:actividades,id_actividad',
            'id_docente'       => 'required|exists:docentes,id', 
            'tema_sesion'      => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'numero_de_sesion' => 'required|integer|min:1',
            'horario_inicio'   => 'required',
            'horario_fin'      => 'required|after:horario_inicio',
            'estado'           => 'required|in:espera,curso,finalizado'
        ]);

        // Se obtiene la actividad para heredar automáticamente su fecha de ejecución
        $actividad = Actividad::findOrFail($request->id_actividad);

        Tema::create([
            'tema_sesion'      => $request->tema_sesion,
            'descripcion'      => $request->descripcion,
            'numero_de_sesion' => $request->numero_de_sesion,
            'fecha'            => $actividad->fecha, // Hereda la fecha de la actividad
            'horario_inicio'   => $request->horario_inicio,
            'horario_fin'      => $request->horario_fin,
            'estado'           => $request->estado,
            'id_docente'       => $request->id_docente,
            'id_actividad'     => $request->id_actividad
        ]);

        return redirect()->back()->with('success', 'La sesión ha sido vinculada correctamente.');
    }

    /**
     * Actualiza una sesión existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_actividad'     => 'required|exists:actividades,id_actividad',
            'id_docente'       => 'required|exists:docentes,id',
            'tema_sesion'      => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'numero_de_sesion' => 'required|integer|min:1',
            'horario_inicio'   => 'required',
            'horario_fin'      => 'required|after:horario_inicio',
            'estado'           => 'required|in:espera,curso,finalizado'
        ]);

        $tema = Tema::findOrFail($id);
        $actividad = Actividad::findOrFail($request->id_actividad);

        $tema->update([
            'tema_sesion'      => $request->tema_sesion,
            'descripcion'      => $request->descripcion,
            'numero_de_sesion' => $request->numero_de_sesion,
            'fecha'            => $actividad->fecha,
            'horario_inicio'   => $request->horario_inicio,
            'horario_fin'      => $request->horario_fin,
            'estado'           => $request->estado,
            'id_docente'       => $request->id_docente,
            'id_actividad'     => $request->id_actividad
        ]);

        return redirect()->back()->with('success', 'La sesión ha sido actualizada correctamente.');
    }

    /**
     * Elimina una sesión.
     */
    public function destroy($id)
    {
        $tema = Tema::findOrFail($id);
        $tema->delete();

        return redirect()->back()->with('success', 'La sesión ha sido eliminada correctamente.');
    }
}