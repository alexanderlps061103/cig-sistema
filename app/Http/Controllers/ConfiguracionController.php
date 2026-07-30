<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Espacio, Trimestre, Feriado, Modalidad, TipoActividad, Actividad};
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    // ==========================================
    // 1. PERIODOS (TRIMESTRES)
    // ==========================================
    
    /**
     * CORREGIDO: Ahora ordena por ID de forma ascendente (1 arriba, luego 2 abajo...)
     */
    public function periodosIndex() {
        $periodos = Trimestre::orderBy('id', 'asc')->paginate(10);
        return view('coordinador.planificacion.periodos', compact('periodos'));
    }

    /**
     * CORREGIDO: Se valida 'estado' y se mapea de 'active'/'inactive' a boolean para la columna 'estado'
     */
    public function periodosStore(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado' => 'required|in:active,inactive',
        ]);

        Trimestre::create([
            'nombre'       => $data['nombre'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin'    => $data['fecha_fin'],
            'estado'       => $data['estado'] === 'active', // Guarda true (1) o false (0)
            'creado_por'   => auth()->user()->persona_id,
        ]);

        return back()->with('success', 'Periodo creado correctamente.');
    }

    /**
     * CORREGIDO: Se procesa la actualización traduciendo 'active'/'inactive' a boolean en la columna 'estado'
     */
    public function periodosUpdate(Request $request, $id) {
        $data = $request->validate([
            'nombre' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado' => 'required|in:active,inactive',
        ]);

        $periodo = Trimestre::findOrFail($id);
        
        $periodo->update([
            'nombre'       => $data['nombre'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin'    => $data['fecha_fin'],
            'estado'       => $data['estado'] === 'active',
        ]);

        return back()->with('success', 'Periodo actualizado.');
    }

    /**
     * CORREGIDO: Se cambia el campo de actualización de 'activo' a 'estado' para que coincida con la BD
     */
    public function periodosToggle($id) {
        $periodo = Trimestre::findOrFail($id);
        
        // El campo real de la base de datos es 'estado'
        $periodo->update([
            'estado' => !$periodo->estado
        ]);

        return back()->with('success', 'Estado del periodo actualizado.');
    }

    // ==========================================
    // 2. MODALIDADES
    // ==========================================
    public function modalidadesIndex() {
        $modalidades = Modalidad::all();
        return view('coordinador.estructura.modalidades', compact('modalidades'));
    }

    public function modalidadesStore(Request $request) {
        Modalidad::create($request->validate(['nombre' => 'required', 'descripcion' => 'nullable']));
        return back()->with('success', 'Modalidad registrada.');
    }

    public function modalidadesUpdate(Request $request, $id) {
        $modalidad = Modalidad::findOrFail($id);
        $modalidad->update($request->all());
        return back()->with('success', 'Modalidad actualizada.');
    }

    // ==========================================
    // 3. TIPOS DE ACTIVIDAD
    // ==========================================
    public function tiposIndex() {
        $tipos = TipoActividad::all();
        return view('coordinador.estructura.tipos', compact('tipos'));
    }

    public function tiposStore(Request $request) {
        TipoActividad::create($request->validate(['nombre' => 'required', 'descripcion' => 'nullable']));
        return back()->with('success', 'Tipo de actividad creado.');
    }

    public function tiposUpdate(Request $request, $id) {
        $tipo = TipoActividad::findOrFail($id);
        $tipo->update($request->all());
        return back()->with('success', 'Tipo actualizado.');
    }

    public function tiposDestroy($id) {
        $tipo = TipoActividad::findOrFail($id);
        if($tipo->actividades()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene actividades registradas.');
        }
        $tipo->delete();
        return back()->with('info', 'Tipo de actividad eliminado.');
    }

    // ==========================================
    // 4. FERIADOS
    // ==========================================
    public function feriadosIndex() {
        $trimestres = Trimestre::orderBy('fecha_inicio', 'desc')->get();
        $feriados = Feriado::orderBy('fecha', 'asc')->get();
        return view('coordinador.planificacion.feriados', compact('feriados', 'trimestres'));
    }

    public function feriadosStore(Request $request) {
        $request->validate(['fecha' => 'required|date', 'descripcion' => 'required']);
        Feriado::create($request->all() + [
            'creado_por' => auth()->user()->persona_id,
            'recurrente' => $request->has('recurrente')
        ]);
        return back()->with('success', 'Día no laboral registrado.');
    }

    public function feriadosDestroy($id) {
        Feriado::findOrFail($id)->delete();
        return back()->with('info', 'Feriado eliminado.');
    }

    // ==========================================
    // 5. AULAS (ESPACIOS)
    // ==========================================
    public function espaciosIndex(Request $request) {
        $espacios = Espacio::all()->map(function($espacio) {
            $ocupado = Actividad::where('espacio_id', $espacio->id)
                                ->whereDate('fecha_actividad', now()->format('Y-m-d'))
                                ->exists();
            $espacio->en_uso = $ocupado;
            return $espacio;
        });

        return view('coordinador.planificacion.aulas', compact('espacios'));
    }

    public function espaciosStore(Request $request) {
        $data = $request->validate([
            'nombre' => 'required',
            'capacidad' => 'required|integer',
            'ubicacion' => 'nullable'
        ]);
        // NOTA DE ATENCIÓN: Si tu tabla 'espacios' también usa la columna 'estado' 
        // en lugar de 'activo' en su migración, deberías cambiar 'activo' por 'estado' aquí.
        Espacio::create($data + ['activo' => true]);
        return back()->with('success', 'Aula registrada.');
    }

    public function espaciosUpdate(Request $request, $id) {
        $espacio = Espacio::findOrFail($id);
        $espacio->update($request->all());
        return back()->with('success', 'Datos del aula actualizados.');
    }

    public function espaciosToggle($id) {
        $espacio = Espacio::findOrFail($id);
        // NOTA DE ATENCIÓN: Al igual que con los periodos, si en la migración de espacios la columna 
        // es 'estado', debes cambiar 'activo' por 'estado' en esta línea.
        $espacio->update(['activo' => !$espacio->activo]);
        return back()->with('info', 'Estado del aula actualizado.');
    }
}