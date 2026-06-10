<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{
    public function dashboard()
    {
        $persona = Auth::user();
        // Sesiones que imparte como ponente
        $sesiones = $persona->sesionesComoPonente()
            ->with('actividad.trimestre')
            ->whereHas('actividad', function ($q) {
                $q->where('estado', '!=', 'cancelada');
            })
            ->orderBy('fecha')
            ->get();

        // Certificados como docente
        $certificados = $persona->certificados()->where('tipo', 'docente_sesion')->latest()->get();

        // Tutorías asignadas (si aplica)
        $tutorias = $persona->tutoriasAsignadas()->where('activo', true)->with('estudiante')->get();

        return view('docente.dashboard', compact('persona', 'sesiones', 'certificados', 'tutorias'));
    }
}
