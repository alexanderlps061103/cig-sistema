<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstudianteController extends Controller
{
    public function dashboard()
    {
        $persona = Auth::user();
        // Inscripciones con la actividad
        $inscripciones = $persona->inscripciones()->with('actividad')->latest()->get();
        // Certificados obtenidos
        $certificados = $persona->certificados()->latest()->get();
        // Datos de estudiante (si es regular)
        $estudiante = $persona->estudiante;

        return view('estudiante.dashboard', compact('persona', 'inscripciones', 'certificados', 'estudiante'));
    }

    public function pasantias()
    {
        $persona = Auth::user();
        // Verificar solicitud de inducción
        $solicitudInduccion = \App\Models\SolicitudInduccion::where('estudiante_id', $persona->id)->first();
        $induccion = $solicitudInduccion ? $solicitudInduccion->inducciones()->first() : null;
        $tutor = \App\Models\TutoresAsignado::where('estudiante_id', $persona->id)->where('activo', true)->first();
        $cartas = $persona->cartasPasantia()->latest()->get();

        return view('estudiante.pasantias', compact('persona', 'solicitudInduccion', 'induccion', 'tutor', 'cartas'));
    }
}
