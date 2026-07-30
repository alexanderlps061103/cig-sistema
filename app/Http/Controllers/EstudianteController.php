<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Inscripcion;
use App\Models\Sesion;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:estudiante']);
    }

    /**
     * Muestra el dashboard del estudiante con sus inscripciones correspondientes
     */
    public function index()
    {
        $persona = auth()->user()->persona;
        
        // Obtenemos el perfil de estudiante asociado a la persona
        $estudiante = $persona ? $persona->estudiante : null;

        // Si tiene un perfil de estudiante, buscamos sus inscripciones por 'id_estudiante'
        $inscripciones = $estudiante 
            ? Inscripcion::where('id_estudiante', $estudiante->id)->with('actividad')->paginate(20)
            : collect();

        // CORREGIDO: Apuntar al archivo exacto "dashboardEstudiante.blade.php"
        return view('estudiante.dashboardEstudiante', compact('inscripciones'));
    }

    /**
     * Página con lector QR para estudiante (JS)
     */
    public function scan(Sesion $sesion)
    {
        return view('estudiante.scan', compact('sesion'));
    }

    /**
     * Registrar la inscripción de un estudiante regular a una actividad
     */
    public function inscribirse(Actividad $actividad, Request $request)
    {
        $persona = auth()->user()->persona;
        $estudiante = $persona ? $persona->estudiante : null;

        if (!$estudiante) {
            return back()->with('error', 'No se pudo procesar la inscripción porque no tienes un perfil de estudiante regular activo.');
        }

        // Comprobar si ya se encuentra inscrito
        $yaInscrito = Inscripcion::where('id_actividad', $actividad->id_actividad)
            ->where('id_estudiante', $estudiante->id)
            ->exists();

        if ($yaInscrito) {
            return back()->with('error', 'Usted ya se encuentra inscrito en esta actividad.');
        }

        // Registrar la inscripción con las columnas correspondientes de la BD
        Inscripcion::create([
            'id_estudiante' => $estudiante->id,
            'id_actividad' => $actividad->id_actividad, // Clave primaria real en actividades
            'fecha_registro' => now()->toDateString(),
            'estado' => 'pendiente'
        ]);

        return back()->with('success', 'Solicitud de inscripción enviada exitosamente.');
    }
}