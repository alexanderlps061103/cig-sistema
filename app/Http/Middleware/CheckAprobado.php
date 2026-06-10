<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAprobado
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar si está logueado
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        // 2. Si es Coordinador (Admin), acceso total
        if ($user->roles()->where('nombre', 'coordinador')->exists()) {
            return $next($request);
        }

        // 3. Si es Estudiante, verificar si ya aprobó la inducción
        if ($user->roles()->where('nombre', 'estudiante')->exists()) {
            // Buscar una solicitud aprobada que tenga una inducción aprobada
            $aprobado = \App\Models\SolicitudInduccion::where('estudiante_id', $user->id)
                ->where('estado', 'aprobada')
                ->whereHas('inducciones', function ($query) {
                    $query->where('aprobada', true);
                })->exists();

            if (!$aprobado) {
                return redirect()->route('estudiante.dashboard')
                    ->with('error', 'Debes completar y aprobar el curso de inducción antes de acceder a las pasantías.');
            }
        }

        // Si no es estudiante ni coordinador (ej. docente sin permiso), podrías denegar
        return $next($request);
    }
}
