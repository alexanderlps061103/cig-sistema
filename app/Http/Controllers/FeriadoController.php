<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Feriado; // crea este modelo y migration

class FeriadoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:coordinador']);
    }

    public function index()
    {
        $feriados = Feriado::orderBy('fecha')->get();
        return view('coordinador.feriados.index', compact('feriados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:255',
            'recurrente' => 'boolean'
        ]);
        Feriado::create($data);
        return back()->with('success','Feriado agregado.');
    }

    public function destroy(Feriado $feriado)
    {
        $feriado->delete();
        return back()->with('success','Feriado eliminado.');
    }
}
