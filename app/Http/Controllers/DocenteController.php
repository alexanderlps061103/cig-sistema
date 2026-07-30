<?php

namespace App\Http\Controllers;

use App\Models\Tema;
use App\Models\Asistencia;
use App\Models\Actividad;
use App\Models\Docente;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AsistenciasExport;

class DocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:docente']);
    }

    public function index()
    {
        $personaId = auth()->user()->persona_id ?? auth()->user()->persona->id;

        // Buscamos el perfil docente de la persona logueada
        $docente = Docente::where('persona_id', $personaId)->first();

        // Buscamos directamente las actividades asociadas a los temas de este docente
        $sesiones = $docente 
            ? Actividad::whereHas('tema', function($query) use ($docente) {
                  $query->where('id_docente', $docente->id);
              })->with(['tema', 'salon'])->get() 
            : collect();

        return view('docente.dashboardDocente', compact('sesiones'));
    }

    // Exportar lista de inscritos/Asistencias en Excel (dinámico)
    public function exportInscritos(Actividad $actividad)
    {
        return Excel::download(new \App\Exports\InscritosExport($actividad->id_actividad), "inscritos_actividad_{$actividad->id_actividad}.xlsx");
    }

    // Ver listado en tabla con búsqueda/paginación (AJAX support)
    public function inscritosTable(Actividad $actividad, Request $request)
    {
        $query = $actividad->inscripciones()->with('persona');

        if ($q = $request->input('q')) {
            $query->whereHas('persona', fn($qq)=> $qq->where('nombres','like',"%{$q}%")->orWhere('apellidos','like',"%{$q}%"));
        }

        if ($order = $request->input('order')) {
            if ($order === 'alfabetico') {
                $query->join('personas','inscripciones.persona_id','=','personas.id')->orderBy('personas.nombres');
            } else {
                $query->orderBy('fecha_inscripcion','desc');
            }
        }

        $inscripciones = $query->paginate(50);
        return response()->json($inscripciones);
    }

    // Permitir al docente tomar asistencia manualmente (ajustado a Tema e id_tema)
    public function tomarAsistenciaManual(Request $request, Tema $tema)
    {
        $data = $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'metodo' => 'nullable|string'
        ]);

        $existe = Asistencia::where('id_tema', $tema->id_tema)->where('persona_id', $data['persona_id'])->first();
        if ($existe) {
            return back()->with('info','Asistencia ya registrada.');
        }

        Asistencia::create([
            'id_tema' => $tema->id_tema,
            'persona_id' => $data['persona_id'],
            'fecha_hora' => now(),
            'metodo' => $data['metodo'] ?? 'manual',
            'registrado_por' => auth()->user()->persona_id ?? auth()->user()->persona->id,
        ]);

        return back()->with('success','Asistencia registrada.');
    }
}