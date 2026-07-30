<?php

namespace App\Http\Controllers;

use App\Traits\RedirectsByRole;
use Illuminate\Http\Request;
use App\Models\Planificacion;
use App\Models\Trimestre;
use App\Models\Actividad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    use RedirectsByRole;

    public function index()
    {
        return redirect($this->redirectToDashboardByRole(auth()->user()));
    }

    public function coordinadorDashboard()
    {
        $now = Carbon::now();
        $anioActual = $now->year;

        // 1. Obtener planificación, trimestres, actividades y relaciones de temas/docentes
        $planificacion = Planificacion::with([
            'trimestres.actividades.salon', 
            'trimestres.actividades.tipo',
            'trimestres.actividades.temas.docente.persona' // Trae las sesiones, el docente y sus datos personales
        ])
        ->where('anio', $anioActual)
        ->first();

        $trimestres = $planificacion ? $planificacion->trimestres : collect();

        // 2. Unificar Actividades y Feriados para el Calendario
        $actividadesMapped = [];

        // A. Cargar Actividades con sus Temas/Sesiones correspondientes
        foreach ($trimestres as $trimestre) {
            foreach ($trimestre->actividades as $actividad) {
                
                // Mapeamos los temas asociados a esta actividad con adaptaciones de compatibilidad
                $sesionesMapped = $actividad->temas->map(function ($tema) {
                    
                    // Fallback de seguridad: si el docente no está precargado por Eloquent, lo buscamos de manera directa
                    $docente = $tema->docente ?? \App\Models\Docente::with('persona')->find($tema->id_docente);
                    $nombreDocente = ($docente && $docente->persona)
                        ? $docente->persona->nombres . ' ' . $docente->persona->apellidos
                        : 'Sin Docente Asignado';

                    // Combinar fecha y hora para evitar que los formateadores del Frontend arrojen "N/A"
                    $fechaStr = ($tema->fecha instanceof Carbon) ? $tema->fecha->format('Y-m-d') : $tema->fecha;
                    $startAtCombined = ($fechaStr && $tema->horario_inicio) ? "{$fechaStr} {$tema->horario_inicio}" : $tema->horario_inicio;
                    $endAtCombined = ($fechaStr && $tema->horario_fin) ? "{$fechaStr} {$tema->horario_fin}" : $tema->horario_fin;

                    return [
                        'id'                => $tema->id_tema,
                        'id_tema'           => $tema->id_tema,
                        'idTema'            => $tema->id_tema,
                        
                        // Tema / Título
                        'tema_sesion'       => $tema->tema_sesion,
                        'temaSesion'        => $tema->tema_sesion,
                        'tema'              => $tema->tema_sesion,
                        'nombre'            => $tema->tema_sesion,
                        'title'             => $tema->tema_sesion,
                        
                        // Descripción
                        'descripcion'       => $tema->descripcion,
                        'description'       => $tema->descripcion,
                        
                        // Número de sesión
                        'numero_de_sesion'  => $tema->numero_de_sesion,
                        'numeroDeSesion'    => $tema->numero_de_sesion,
                        'numero_sesion'     => $tema->numero_de_sesion,
                        'numeroSesion'      => $tema->numero_de_sesion,
                        'numero'            => $tema->numero_de_sesion,
                        
                        // Horarios y fechas combinados (Compatibilidad con formateadores de fecha en JS)
                        'horario_inicio'    => $tema->horario_inicio,
                        'horarioInicio'     => $tema->horario_inicio,
                        'horario_fin'       => $tema->horario_fin,
                        'horarioFin'        => $tema->horario_fin,
                        
                        'hora_inicio'       => $tema->horario_inicio,
                        'horaInicio'        => $tema->horario_inicio,
                        'hora_fin'          => $tema->horario_fin,
                        'horaFin'           => $tema->horario_fin,
                        
                        'start_at'          => $startAtCombined,
                        'startAt'           => $startAtCombined,
                        'end_at'            => $endAtCombined,
                        'endAt'             => $endAtCombined,
                        
                        // Estado
                        'estado'            => $tema->estado,
                        'status'            => $tema->estado,
                        
                        // Docente
                        'id_docente'        => $tema->id_docente,
                        'idDocente'         => $tema->id_docente,
                        'docente'           => $nombreDocente,
                        'nombre_docente'    => $nombreDocente,
                        'nombreDocente'     => $nombreDocente,
                        'teacher'           => $nombreDocente,
                    ];
                })->toArray();

                $actividadesMapped[] = [
                    'id'                   => $actividad->id_actividad,
                    'type'                 => 'actividad',
                    'nombre'               => $actividad->nombre,
                    'descripcion'          => $actividad->descripcion,
                    'planificacion_nombre' => $planificacion->titulo,
                    'anio'                 => $planificacion->anio,
                    'trimestre_nombre'     => $trimestre->nombre,
                    'id_trimestre'         => $actividad->id_trimestre,
                    'id_salon'             => $actividad->id_salon,
                    'id_modalidad'         => $actividad->id_modalidad,
                    'id_tipo_actividad'    => $actividad->id_tipo_actividad,
                    'id_organizador'       => $actividad->id_organizador,
                    'modalidad'            => $actividad->modalidad ?? 'N/A', 
                    'tipo'                 => $actividad->tipo->nombre ?? 'N/A',
                    'aula'                 => $actividad->salon->nombre ?? 'N/A',
                    'fecha'                => Carbon::parse($actividad->fecha)->format('Y-m-d'),
                    'hora_inicio'          => $actividad->hora_inicio,
                    'hora_fin'             => $actividad->hora_fin,
                    'horario'              => $actividad->horario ?? ($actividad->hora_inicio . ' a ' . $actividad->hora_fin),
                    'estado'               => $actividad->estado,
                    'sesiones_conteo'      => $actividad->temas->count(),
                    'sesiones'             => $sesionesMapped, // Pasado al frontend
                    'temas'                => $sesionesMapped,  // Alias alternativo
                ];
            }
        }

        // B. Cargar Feriados de la Base de Datos
        if (Schema::hasTable('feriados')) {
            $feriados = DB::table('feriados')->get();
            foreach ($feriados as $feriado) {
                $actividadesMapped[] = [
                    'id'          => $feriado->id_feriado ?? $feriado->id,
                    'type'        => 'feriado',
                    'nombre'      => 'Feriado: ' . ($feriado->nombre ?? $feriado->descripcion),
                    'fecha'       => Carbon::parse($feriado->fecha)->format('Y-m-d'),
                    'descripcion' => $feriado->descripcion ?? 'Día no laborable.',
                ];
            }
        }

        // 3. Obtener variables auxiliares necesarias para rellenar los selects de los modales
        $docentes = \App\Models\Docente::with('persona')->get();
        $salones = DB::table('salones')->where('estado', true)->get();
        $modalidades = DB::table('modalidades')->where('estado', true)->get();
        $tipoActividades = DB::table('tipo_actividades')->where('estado', true)->get();
        $tipoDocumentos = DB::table('tipo_documentos')->where('estado', true)->get();
        $temas = DB::table('temas')->get();

        // 4. KPIs rápidos (Nota: para sesiones contamos registros en 'temas' ya que allí guardas el contenido)
        $counts = [
            'actividades'              => Actividad::count(),
            'sesiones'                 => DB::table('temas')->count() ?? 0,
            'espacios'                 => DB::table('salones')->count() ?? 0,
            'inscripciones_pendientes' => DB::table('inscripciones')->where('estado', 'pendiente')->count() ?? 0,
        ];

        return view('coordinador.dashboard', compact(
            'trimestres', 
            'actividadesMapped', 
            'counts', 
            'docentes', 
            'salones', 
            'modalidades', 
            'tipoActividades', 
            'tipoDocumentos', 
            'temas'
        ));
    }
}