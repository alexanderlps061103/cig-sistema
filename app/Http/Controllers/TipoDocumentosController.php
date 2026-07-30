<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean'
        ]);

        $tipo = TipoDocumento::create($data);
        return response()->json(['success' => true, 'data' => $tipo]);
    }
}
