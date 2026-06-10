<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinadorController extends Controller
{
    public function dashboard()
    {
        $persona = Auth::user();
        // Próximas actividades planificadas
        $actividades = \App\Models\Actividad::where('estado', 'planificada')
            ->orWhere('estado', 'activa')
            ->with('trimestre', 'tipoActividad', 'espacio')
            ->latest('fecha_actividad')
            ->take(10)
            ->get();

        // Solicitudes de inducción pendientes
        $solicitudesPendientes = \App\Models\SolicitudInduccion::where('estado', 'pendiente')
            ->with('estudiante')
            ->count();

        // Solicitudes de empleo pendientes
        $empleosPendientes = \App\Models\SolicitudEmpleo::where('estado', 'pendiente')->count();

        return view('coordinador.dashboard', compact('persona', 'actividades', 'solicitudesPendientes', 'empleosPendientes'));
    }
}
