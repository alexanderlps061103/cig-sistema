<?php

namespace App\Traits;

trait RedirectsByRole
{
    public function redirectToDashboardByRole($usuario)
    {
        $persona = $usuario->persona;
        if (!$persona) {
            return redirect()->route('login')->withErrors(['email' => 'No se encontró un perfil asociado a este usuario.']);
        }

        // Obtener todos los roles que tiene asignados en minúsculas
        $roles = $persona->roles->pluck('nombre')->map(fn($r) => strtolower($r))->toArray();

        // 1. Si ya tiene un rol activo seleccionado en sesión y realmente lo posee, respetarlo
        $roleActivo = session('role_active'); // usando el estándar de tu DashboardController
        if ($roleActivo && in_array(strtolower($roleActivo), $roles)) {
            return route(strtolower($roleActivo) . '.dashboard');
        }

        // 2. Jerarquía por defecto para el primer inicio de sesión
        $prioridad = ['rector', 'coordinador', 'docente', 'estudiante', 'publico'];
        $roleSeleccionado = 'publico'; // Rol base por defecto

        foreach ($prioridad as $p) {
            if (in_array($p, $roles)) {
                $roleSeleccionado = $p;
                break;
            }
        }

        // Guardamos en sesión el rol activo inicial
        session(['role_active' => $roleSeleccionado]);

        return route($roleSeleccionado . '.dashboard');
    }
}