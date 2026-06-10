<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $persona = Auth::user();

        if (!$persona) {
            return redirect('/login');
        }

        // Obtener el rol activo desde la sesión
        $rolActivo = session('rol_activo');

        if (!$rolActivo) {
            // Si no tiene rol activo, redirigir a selector
            return redirect()->route('seleccionar-rol');
        }

        // Verificar que la persona posea ese rol
        if (!in_array($rolActivo, $roles)) {
            // También podemos verificar que el rol esté en la lista de roles permitidos
            abort(403, 'Acceso no autorizado para este rol.');
        }

        // Verificar que la persona realmente tenga ese rol en la BD
        if (!$persona->roles()->where('nombre', $rolActivo)->exists()) {
            abort(403, 'No tienes asignado este rol.');
        }

        return $next($request);
    }
}
