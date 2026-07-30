<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;

class PublicController extends Controller
{
    public function index()
    {
        // Si el usuario ya está autenticado, lo redirige automáticamente a su dashboard
        if (auth()->check()) {
            return redirect()->route('publico.dashboard'); // O la ruta correspondiente a su dashboard
        }

        // Redirige explícitamente a la ruta con nombre 'login' para que cambie la URL en el navegador
        return redirect()->route('login');
        
        // Nota: Si no tiene una ruta con el nombre 'login', puede usar la URL directa:
        // return redirect('/login');
    }
}