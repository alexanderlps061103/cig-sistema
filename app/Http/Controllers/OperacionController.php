<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Actividad, Certificado, Encuesta, RespuestaEncuesta};
use Illuminate\Support\Facades\DB;

class OperacionController extends Controller
{
    public function generarCertificadosMasivos($actividad_id) {
        $actividad = Actividad::with('inscripciones.persona')->findOrFail($actividad_id);

        foreach ($actividad->inscripciones as $insc) {
            // Lógica: Si asistió a las sesiones, crear certificado
            Certificado::create([
                'persona_id' => $insc->persona_id,
                'actividad_id' => $actividad->id,
                'tipo' => 'participante',
                'codigo_verificacion' => strtoupper(bin2hex(random_bytes(5))),
                'fecha_emision' => now(),
            ]);
        }

        // También generar para los Ponentes
        foreach ($actividad->sesiones as $sesion) {
            foreach ($sesion->ponentes as $ponente) {
                Certificado::create([
                    'persona_id' => $ponente->persona_id ?? $ponente->docente->persona_id,
                    'actividad_id' => $actividad->id,
                    'sesion_id' => $sesion->id,
                    'tipo' => 'ponente',
                    'codigo_verificacion' => strtoupper(bin2hex(random_bytes(5))),
                    'fecha_emision' => now(),
                ]);
            }
        }

        return back()->with('success', 'Certificados generados para todos los participantes y ponentes.');
    }

    public function verGraficas($id) {
        $encuesta = Encuesta::with('preguntas.respuestas')->findOrFail($id);
        // Aquí preparas los datos para Chart.js
        return view('coordinador.operaciones.graficas', compact('encuesta'));
    }
}
