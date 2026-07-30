<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planificacion;

class PlanificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $registros = Planificacion::all();
        return view('coordinador.entidades_crud_y_procesos.planificacion', compact('registros'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validamos únicamente los campos que vienen de la vista (Título y Fecha)
        $validatedData = $request->validate([
            'titulo'         => 'required|string|max:255',
            'fecha_creacion' => 'required|date',
        ]);

        // 2. Extraemos el año de la fecha seleccionada y lo añadimos al arreglo de datos
        $validatedData['anio'] = date('Y', strtotime($validatedData['fecha_creacion']));

        // 3. Creamos el registro en la base de datos
        Planificacion::create($validatedData);

        return redirect()->back()->with('success', 'Planificación guardada correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. Validamos únicamente los campos editables de la vista
        $validatedData = $request->validate([
            'titulo'         => 'required|string|max:255',
            'fecha_creacion' => 'required|date',
        ]);

        // 2. Volvemos a extraer el año automáticamente si la fecha de creación se actualiza
        $validatedData['anio'] = date('Y', strtotime($validatedData['fecha_creacion']));

        $planificacion = Planificacion::findOrFail($id);
        $planificacion->update($validatedData);

        return redirect()->back()->with('success', 'Planificación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $planificacion = Planificacion::findOrFail($id);
        $planificacion->delete();

        return redirect()->back()->with('success', 'Planificación eliminada correctamente.');
    }
}