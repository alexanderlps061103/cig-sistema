<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    // Estructuración y respuesta en formato HTML/Plantilla para renderizados del sistema
    public function generarDocumento($id_inscripcion)
    {
        $inscripcion = Inscripcion::with(['actividad.trimestre', 'documento.firma', 'documento.sello'])->findOrFail($id_inscripcion);

        // Se retorna una estructura en HTML crudo para ser exportada o impresa
        return view('formatos.documento_html', compact('inscripcion'));
    }
}
