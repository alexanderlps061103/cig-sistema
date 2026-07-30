<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;
use App\Models\Inscripcion;
use Carbon\Carbon;

class PublicoController extends Controller
{
    /**
     * Muestra el dashboard del público general con las actividades del mes en curso.
     */
    public function dashboard()
    {
        $now = Carbon::now();
        $mesActual = $now->month;
        $anioActual = $now->year;

        // CONSULTA DE ACTIVIDADES
        // Nota: Si para pruebas locales deseas ver todas las actividades sin importar la fecha actual,
        // puedes comentar las líneas ->whereYear y ->whereMonth de abajo.
        $actividades = Actividad::whereYear('fecha', $anioActual)
            ->whereMonth('fecha', $mesActual)
            ->where('estado', '!=', 'cancelada')
            ->with(['tipo', 'salon', 'modalidadRelacion'])
            ->get();

        $persona = auth()->user()->persona;
        $estudiante = $persona ? $persona->estudiante : null;

        // Obtener actividades en las que el usuario autenticado ya se encuentra inscrito
        $inscripcionesUsuario = [];
        if ($persona) {
            $inscripcionesUsuario = Inscripcion::where(function ($query) use ($persona, $estudiante) {
                if ($estudiante) {
                    $query->where('id_estudiante', $estudiante->id);
                } else {
                    $query->where('id_publico_general', $persona->id);
                }
            })->pluck('id_actividad')->toArray();
        }

        return view('publico.dashboardPublico', compact('actividades', 'inscripcionesUsuario', 'now'));
    }

    /**
     * Procesa la inscripción a una actividad, validando y completando el perfil si es necesario.
     */
    public function inscribir(Actividad $actividad, Request $request)
    {
        $user = auth()->user();
        $persona = $user->persona;

        if (!$persona) {
            return back()->withErrors(['error' => 'No se encontró un perfil de persona asociado a su cuenta.']);
        }

        // Si se envió el formulario para completar o actualizar los datos del perfil
        if ($request->has('completar_perfil')) {
            
            // Validación formal estricta
            $request->validate([
                'nombres'   => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s.,()\-#]+$/u'],
                'apellidos' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s.,()\-#]+$/u'],
                'cedula'    => ['required', 'string', 'max:20', 'regex:/^[0-9a-zA-Z\-#]+$/', 'unique:personas,cedula,' . $persona->id],
                'telefono'  => ['required', 'string', 'max:25', 'regex:/^[0-9a-zA-Z\s+\-#()]+$/'],
            ], [
                'nombres.required'   => 'El campo nombres es obligatorio.',
                'nombres.regex'      => 'Los nombres contienen caracteres especiales no permitidos (solo se permiten letras, espacios, puntos, comas, guiones, numerales y paréntesis).',
                'apellidos.required' => 'El campo apellidos es obligatorio.',
                'apellidos.regex'    => 'Los apellidos contienen caracteres especiales no permitidos (solo se permiten letras, espacios, puntos, comas, guiones, numerales y paréntesis).',
                'cedula.required'    => 'El campo cédula de identidad es obligatorio.',
                'cedula.regex'       => 'La cédula de identidad contiene un formato inválido (solo se permiten números, letras, guiones y numerales).',
                'cedula.unique'      => 'La cédula de identidad ya se encuentra registrada en el sistema.',
                'telefono.required'  => 'El campo teléfono de contacto es obligatorio.',
                'telefono.regex'     => 'El teléfono contiene caracteres no válidos.',
            ]);

            // Actualización de la información básica en la tabla personas
            $persona->update([
                'nombres'   => $request->nombres,
                'apellidos' => $request->apellidos,
                'cedula'    => $request->cedula,
                'telefono'  => $request->telefono,
            ]);
        }

        // Validación de consistencia de los datos en el backend
        if (empty($persona->cedula) || empty($persona->telefono) || empty($persona->nombres) || empty($persona->apellidos)) {
            return back()->withErrors(['error' => 'La inscripción no pudo procesarse debido a que su perfil cuenta con campos obligatorios vacíos.'])
                         ->withInput(); // Mantiene los campos en el formulario en caso de fallar
        }

        // Comprobación de perfil del estudiante
        $estudiante = $persona->estudiante;

        // Comprobación de inscripción duplicada corregida (Evita la ambigüedad en el OR)
        $yaInscrito = Inscripcion::where('id_actividad', $actividad->id_actividad)
            ->where(function ($query) use ($persona, $estudiante) {
                if ($estudiante) {
                    $query->where('id_estudiante', $estudiante->id);
                } else {
                    $query->where('id_publico_general', $persona->id);
                }
            })->exists();

        if ($yaInscrito) {
            return back()->withErrors(['error' => 'Usted ya se encuentra inscrito en esta actividad.']);
        }

        // Registro de la inscripción en base de datos
        Inscripcion::create([
            'fecha_registro'     => Carbon::now()->toDateString(),
            'id_estudiante'      => $estudiante ? $estudiante->id : null,
            'id_publico_general' => !$estudiante ? $persona->id : null,
            'id_actividad'       => $actividad->id_actividad,
            'estado'             => 'pendiente'
        ]);

        session()->flash('success', '¡Su solicitud de inscripción se ha registrado exitosamente!');

        return back();
    }
}