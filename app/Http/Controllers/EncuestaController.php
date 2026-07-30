<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Encuesta;
use App\Models\PreguntaEncuesta;
use App\Models\RespuestaEncuesta;

class EncuestaController extends Controller
{
    public function create(Actividad $actividad)
    {
        return view('coordinador.encuestas.create', compact('actividad'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'actividad_id' => 'required|exists:actividades,id',
            'descripcion' => 'nullable|string',
            'fecha_limite' => 'nullable|date'
        ]);

        $encuesta = Encuesta::create($data);
        // crear preguntas si vienen
        if ($request->filled('preguntas')) {
            foreach ($request->input('preguntas') as $p) {
                PreguntaEncuesta::create([
                    'encuesta_id' => $encuesta->id,
                    'texto' => $p['texto'],
                    'tipo' => $p['tipo'] ?? 'texto',
                    'opciones' => $p['opciones'] ?? null
                ]);
            }
        }

        return back()->with('success','Encuesta creada.');
    }

    public function responder(Request $request, Encuesta $encuesta)
    {
        $respuestas = $request->input('respuestas', []);
        foreach ($respuestas as $preguntaId => $valor) {
            RespuestaEncuesta::create([
                'pregunta_id' => $preguntaId,
                'persona_id' => auth()->user()->persona->id ?? null,
                'valor' => is_array($valor) ? json_encode($valor) : $valor
            ]);
        }
        return back()->with('success','Gracias por responder la encuesta.');
    }
}
