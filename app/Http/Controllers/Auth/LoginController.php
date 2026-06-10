<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Manejar el intento de login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentar autenticar con el guard 'web' (configurado con provider personas)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $persona = Auth::user();

            // Verificar si el usuario está activo
            if (!$persona->activo) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Tu cuenta ha sido desactivada. Contacta al administrador.',
                ]);
            }

            $request->session()->regenerate();

            // Redirigir a la selección de rol (el middleware se encargará)
            return redirect()->intended(route('seleccionar-rol'));
        }

        // Fallo de autenticación
        throw ValidationException::withMessages([
            'email' => 'Las credenciales no coinciden.',
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
