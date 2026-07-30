<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Docente;
use App\Models\Profesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocenteInvitadoController extends Controller
{
    /**
     * Muestra el formulario público de registro de docentes invitados
     */
    public function create()
    {
        $profesiones = Profesion::where('estado', true)->orderBy('nombre')->get();
        // CORREGIDO: Retorna la vista pública directa
        return view('docente_invitado', compact('profesiones'));
    }

    /**
     * Almacena al docente invitado en la base de datos (Sin crear credenciales de usuario)
     */
    public function store(Request $request)
    {
        $request->validate([
            'cedula' => ['required', 'string', 'max:20', 'unique:personas,cedula'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'sexo' => ['nullable', 'string', 'max:10'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'profesion_id' => ['required', 'exists:profesiones,id'],
        ]);

        DB::beginTransaction();
        try {
            // Se registra como Persona
            $persona = Persona::create([
                'cedula' => $request->cedula,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'telefono' => $request->telefono,
                'sexo' => $request->sexo,
                'verified_at' => now()
            ]);

            // Se asocia directamente al perfil Docentes
            Docente::create([
                'persona_id' => $persona->id,
                'profesion_id' => $request->profesion_id,
            ]);

            DB::commit();

            return back()->with('success', 'Te has registrado exitosamente como docente invitado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Ocurrió un error al procesar tu registro: ' . $e->getMessage()]);
        }
    }
}