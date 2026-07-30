<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trimestre;
use App\Models\Planificacion;

class TrimestreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        // Optimización: Carga previa de la relación para evitar consultas N+1 en la vista
        $registros = Trimestre::with('planificacion')->get();
        $planificaciones = Planificacion::all(); // Carga las opciones necesarias para los selectores de los modales
        
        return view('coordinador.entidades_crud_y_procesos.trimestre', compact('registros', 'planificaciones'));
    }

    /**
     * Store a newly created trimester in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre'           => 'required|string|max:255',
            'fecha_inicio'     => 'required|date',
            'fecha_fin'        => 'required|date|after_or_equal:fecha_inicio',
            'id_planificacion' => 'required|exists:planificaciones,id_planificacion',
        ]);

        Trimestre::create($validatedData);

        return redirect()->back()->with('success', 'Trimestre registrado correctamente.');
    }

    /**
     * Update the specified trimester in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nombre'           => 'required|string|max:255',
            'fecha_inicio'     => 'required|date',
            'fecha_fin'        => 'required|date|after_or_equal:fecha_inicio',
            'id_planificacion' => 'required|exists:planificaciones,id_planificacion',
        ]);

        $trimestre = Trimestre::findOrFail($id);
        $trimestre->update($validatedData);

        return redirect()->back()->with('success', 'Trimestre actualizado correctamente.');
    }

    /**
     * Remove the specified trimester from storage.
     */
    public function destroy($id)
    {
        $trimestre = Trimestre::findOrFail($id);
        $trimestre->delete();

        return redirect()->back()->with('success', 'Trimestre eliminado correctamente.');
    }
}