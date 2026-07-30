<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SolicitudInduccion;

class CheckAprobado
{
    /**
     * Verifica que el estudiante tenga la inducción aprobada.
     *
     * @param  string|null  $redirectTo  Ruta a la que redirigir si no cumple
     */
    public function handle(Request $request, Closure $next, ?string $redirectTo = null, string $errorMessage = 'Debes completar y aprobar la inducción antes de acceder.')
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $persona = $user->persona;

        if (!$persona) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Usuario sin datos personales.']);
        }

        // Rector y Coordinador: acceso total
        if ($persona->roles()->whereIn('nombre', ['rector', 'coordinador'])->exists()) {
            return $next($request);
        }

        // Estudiante: debe tener solicitud de inducción aprobada
        if ($persona->roles()->where('nombre', 'estudiante')->exists()) {
            $estudiante = $persona->estudiante;

            if (!$estudiante) {
                return redirect()->route($redirectTo ?? 'estudiante.dashboard')
                    ->with('error', 'Perfil de estudiante no encontrado. Contacta al administrador.');
            }

            $tieneAprobada = SolicitudInduccion::where('estudiante_id', $estudiante->id)
                ->where('estado', 'aprobada')
                ->exists();

            if (!$tieneAprobada) {
                return redirect()->route($redirectTo ?? 'estudiante.dashboard')
                    ->with('error', $errorMessage);
            }
        }

        // Docente, público u otros: continúan sin restricción
        return $next($request);
    }
}
