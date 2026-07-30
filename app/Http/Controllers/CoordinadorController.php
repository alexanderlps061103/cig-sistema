<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Actividad, Sesion, Salon, Inscripcion, SolicitudInduccion, Certificado, Estudiante, Persona, Feriado};
use App\Models\Planificacion;
use App\Models\Trimestre;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CoordinadorController extends Controller
{
    /**
     * Dashboard Principal del Coordinador
     */
    public function dashboard()
    {
        $counts = [
            'actividades' => Actividad::count(),
            'sesiones' => \App\Models\Tema::count(), 
            'espacios' => Salon::where('estado', true)->count(),
            'inscripciones_pendientes' => Inscripcion::where('estado', 'pendiente')->count(),
        ];

        $now = Carbon::now();
        $anioActual = $now->year;

        // Eager loading de la jerarquía completa
        $planificacion = Planificacion::with(['trimestres.actividades.salon', 'trimestres.actividades.tipo', 'trimestres.actividades.temas.docente.persona'])
            ->where('anio', $anioActual)
            ->first();

        $trimestres = $planificacion ? $planificacion->trimestres : collect();

        // Mapear Actividades y Feriados para el Calendario
        $actividadesMapped = [];

        foreach ($trimestres as $trimestre) {
            foreach ($trimestre->actividades as $actividad) {
                $sesionesConteo = $actividad->temas->count();

                $actividadesMapped[] = [
                    'id'                   => $actividad->id_actividad,
                    'type'                 => 'actividad',
                    'nombre'               => $actividad->nombre,
                    'planificacion_nombre' => $planificacion->titulo,
                    'anio'                 => $planificacion->anio,
                    'trimestre_nombre'     => $trimestre->nombre,
                    'modalidad'            => $actividad->modalidadRelacion->nombre_modalidad ?? 'Presencial', 
                    'tipo'                 => $actividad->tipo->nombre ?? 'N/A',
                    'aula'                 => $actividad->salon->nombre ?? 'N/A',
                    'fecha'                => $actividad->fecha ? $actividad->fecha->format('Y-m-d') : null,
                    'horario'              => Carbon::parse($actividad->hora_inicio)->format('g:i A') . ' a ' . Carbon::parse($actividad->hora_fin)->format('g:i A'),
                    'sesiones_conteo'      => $sesionesConteo,
                    'temas'                => $actividad->temas->map(function($t) {
                        return [
                            'tema_sesion' => $t->tema_sesion,
                            'docente_nombre' => ($t->docente->persona->nombres ?? 'No') . ' ' . ($t->docente->persona->apellidos ?? 'Asignado'),
                            'hora_inicio' => Carbon::parse($t->horario_inicio)->format('g:i A'),
                            'hora_fin' => Carbon::parse($t->horario_fin)->format('g:i A'),
                        ];
                    })
                ];
            }
        }

        // Agregar Feriados de la Base de Datos
        $feriados = Feriado::all();
        foreach ($feriados as $feriado) {
            $fechaFeriado = $feriado->fecha instanceof Carbon ? $feriado->fecha : Carbon::parse($feriado->fecha);
            $actividadesMapped[] = [
                'id'          => $feriado->id_feriado ?? $feriado->id,
                'type'        => 'feriado',
                'nombre'      => 'Feriado: ' . ($feriado->nombre ?? $feriado->descripcion),
                'fecha'       => $fechaFeriado->format('Y-m-d'),
                'descripcion' => $feriado->descripcion ?? 'Día festivo nacional.',
            ];
        }

        // Obtener listas auxiliares
        $salones = Salon::where('estado', true)->get();
        
        $modalidades = DB::table('modalidades')
            ->select('id_modalidad', 'nombre_modalidad as nombre', 'estado')
            ->get();

        $tipoActividades = DB::table('tipo_actividades')->get();
        $tipoDocumentos = DB::table('tipo_documentos')->get();
        $temas = DB::table('temas')->get();

        // Obtener docentes con Eager Loading asegurando que tengan una persona asociada para evitar excepciones
        $docentes = \App\Models\Docente::with('persona')
            ->whereHas('persona')
            ->get();

        return view('coordinador.dashboardCoordinador', compact(
            'counts', 
            'trimestres', 
            'actividadesMapped', 
            'salones', 
            'modalidades', 
            'tipoActividades', 
            'tipoDocumentos', 
            'temas',
            'docentes'
        ));
    }

    /**
     * Apartado: El Coordinador actúa como Docente/Ponente
     */
    public function misSesiones()
    {
        $personaId = auth()->user()->persona_id;
        $sesiones = Sesion::whereHas('ponentes', function($q) use ($personaId) {
            $q->where('persona_id', $personaId);
        })->with('actividad')->get();

        return view('coordinador.personal.sesiones', compact('sesiones'));
    }

    /**
     * Apartado: Certificados que el Coordinador ha recibido
     */
    public function misCertificados()
    {
        $certificados = Certificado::where('persona_id', auth()->user()->persona_id)
            ->with(['actividad', 'sesion'])
            ->latest()
            ->get();

        return view('coordinador.personal.certificados', compact('certificados'));
    }

    /**
     * Apartado: Tutorías de pasantías asignadas al Coordinador
     */
    public function misTutorias()
    {
        $docente = auth()->user()->persona->docentes;

        if (!$docente) {
            return back()->with('error', 'No tienes un perfil de docente activo para ver tutorías.');
        }

        $tutorias = \App\Models\TutorAsignado::where('docente_id', $docente->id)
            ->with(['estudiante.persona', 'induccion'])
            ->get();

        return view('coordinador.personal.tutorias', compact('tutorias'));
    }

    /**
     * Muestra el listado de actividades y los participantes inscritos de cada una.
     */
    public function listarActividadesConInscritos()
    {
        // Cargamos todas las actividades junto con su tipo, salón, modalidad 
        // y los inscritos asociados (con perfiles de estudiante y personas).
        $actividades = Actividad::with([
            'tipo',
            'salon',
            'modalidadRelacion',
            'inscripciones.estudiante.persona',
            'inscripciones.publicoGeneral'
        ])->orderBy('fecha', 'desc')->get();

        // CORREGIDO: Ahora retorna correctamente la vista 'ListadoInscripcion'
        return view('coordinador.ListadoInscripcion', compact('actividades'));
    }
}