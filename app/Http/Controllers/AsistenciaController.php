<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function registrarAsistencia(Request $request)
    {
        $data = $request->validate([
            'estado' => 'required|string',
            'verificacion_qr' => 'required|boolean'
        ]);

        $asistencia = Asistencia::create($data);
        return response()->json(['success' => true, 'data' => $asistencia]);
    }
}
