<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudInduccion;
use App\Models\CartaPasantia;
use Illuminate\Support\Facades\Auth;

class PasantiaController extends Controller
{
    // El Estudiante envía la solicitud
    public function enviarSolicitud(Request $request)
    {
        $request->validate([
            'carta_aprobacion' => 'required|mimes:pdf,jpg,png|max:2048',
            'carnet' => 'required|image|max:1024',
        ]);

        // Guardar archivos
        $rutaCarta = $request->file('carta_aprobacion')->store('pasantias/cartas', 'public');
        $rutaCarnet = $request->file('carnet')->store('pasantias/carnets', 'public');

        SolicitudInduccion::create([
            'estudiante_id' => Auth::user()->persona->estudiante->id,
            'ruta_carta_aprobacion' => $rutaCarta,
            'ruta_carnet' => $rutaCarnet,
            'estado' => 'pendiente',
        ]);

        return back()->with('status', 'Solicitud enviada. Espera la revisión de la coordinación.');
    }

    // La Coordinadora aprueba y verifica el límite de 3 cartas
    public function aprobarSolicitud($id)
    {
        $solicitud = SolicitudInduccion::findOrFail($id);
        $solicitud->update(['estado' => 'aprobado']);

        // Aquí podrías crear automáticamente la primera carta de solicitud
        // Pero validando que no tenga más de 3 activas
        $conteoCartas = CartaPasantia::where('estudiante_id', $solicitud->estudiante_id)
            ->where('status', 'activa')
            ->count();

        if ($conteoCartas < 3) {
            // Lógica para generar carta...
        }

        return back()->with('status', 'Estudiante aprobado para inducción.');
    }
}
