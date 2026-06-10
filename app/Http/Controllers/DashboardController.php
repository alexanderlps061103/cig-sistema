<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function selectRole()
    {
        $persona = Auth::user();
        $roles = $persona->roles;

        if ($roles->count() === 1) {
            session(['rol_activo' => $roles->first()->nombre]);
            return $this->redirectToDashboard($roles->first()->nombre);
        }

        return view('auth.select-role', compact('roles'));
    }

    public function setRole(Request $request)
    {
        $request->validate(['rol' => 'required|string']);
        $rolNombre = $request->rol;

        if (Auth::user()->roles()->where('nombre', $rolNombre)->exists()) {
            session(['rol_activo' => $rolNombre]);
            return $this->redirectToDashboard($rolNombre);
        }

        return back()->withErrors(['rol' => 'Rol no válido']);
    }

    private function redirectToDashboard($rol)
    {
        return redirect()->route("$rol.dashboard");
    }
}
