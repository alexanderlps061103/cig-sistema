<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\RedirectsByRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    use RedirectsByRole;

    /**
     * Muestra el formulario de login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Inicia sesión con un usuario ya validado por LoginRequest.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $usuario = Auth::user();

        // Validar si el usuario fue desactivado por un Rector
        if (!$usuario->activo) {
            Auth::logout();
            return back()->withErrors(['email' => 'Tu cuenta ha sido desactivada.']);
        }

        return redirect($this->redirectToDashboardByRole($usuario));
    }

    /**
     * Cierra la sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
