<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Role;
use App\Models\Estudiante;
use App\Models\Carrera;
use App\Models\Profesion;
use App\Models\ExpedienteEstudiante;
use App\Models\SolicitudEmpleo;
use App\Models\Curriculum;
use App\Traits\RedirectsByRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    use RedirectsByRole;

    public function create()
    {
        $profesiones = Profesion::where('estado', true)->orderBy('nombre')->get();
        $carreras = Carrera::where('estado', true)->orderBy('nombre')->get();
        return view('auth.register', compact('profesiones', 'carreras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_registro' => ['required', 'in:estudiante_externo,estudiante_regular,aspirante_docente'],
            'cedula' => ['required', 'string', 'max:20', 'unique:personas,cedula'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'sexo' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Validaciones dinámicas según selección
            'carrera_id' => ['required_if:tipo_registro,estudiante_regular', 'nullable', 'exists:carreras,id'],
            'foto_selfie' => ['required_if:tipo_registro,estudiante_regular', 'nullable', 'image', 'max:3072'],
            'notas_certificadas' => ['required_if:tipo_registro,estudiante_regular', 'nullable', 'mimes:pdf', 'max:5120'],
            
            'profesion_id' => ['required_if:tipo_registro,aspirante_docente', 'nullable', 'exists:profesiones,id'],
            'cv_archivo' => ['required_if:tipo_registro,aspirante_docente', 'nullable', 'mimes:pdf', 'max:5120'],
            'identificacion_png' => ['required_if:tipo_registro,aspirante_docente', 'nullable', 'image', 'max:3072'],
        ]);

        DB::beginTransaction();
        try {
            // 1. Registrar Persona
            $persona = Persona::create([
                'cedula' => $request->cedula,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'telefono' => $request->telefono,
                'sexo' => $request->sexo,
            ]);

            // Guardar Selfie en 'foto' si es estudiante regular
            if ($request->tipo_registro === 'estudiante_regular' && $request->hasFile('foto_selfie')) {
                $fotoPath = $request->file('foto_selfie')->store('fotos_selfies', 'public');
                $persona->update(['foto' => $fotoPath]);
            }

            // Guardar Cédula en 'cedula_imagen' si es aspirante a docente
            if ($request->tipo_registro === 'aspirante_docente' && $request->hasFile('identificacion_png')) {
                $cedulaPath = $request->file('identificacion_png')->store('identificaciones', 'public');
                $persona->update(['cedula_imagen' => $cedulaPath]);
            }

            // 2. Registrar Cuenta de Usuario
            $usuario = Usuario::create([
                'persona_id' => $persona->id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'activo' => true,
            ]);

            // 3. Configurar perfil de negocio
            if ($request->tipo_registro === 'estudiante_regular') {
                $role = Role::where('nombre', 'estudiante')->first();

                $estudiante = Estudiante::create([
                    'persona_id' => $persona->id,
                    'carrera_id' => $request->carrera_id,
                    'es_regular' => true,
                ]);

                // Guardar Notas Certificadas en el Expediente
                $notasPath = $request->hasFile('notas_certificadas')
                    ? $request->file('notas_certificadas')->store('expedientes/notas', 'public')
                    : null;

                ExpedienteEstudiante::create([
                    'estudiante_id' => $estudiante->id,
                    'carrera_id' => $request->carrera_id,
                    'ruta_notas_certificadas' => $notasPath,
                    'estado_solicitud' => 'pendiente'
                ]);

            } elseif ($request->tipo_registro === 'aspirante_docente') {
                $role = Role::where('nombre', 'publico')->first();

                $cvPath = $request->hasFile('cv_archivo')
                    ? $request->file('cv_archivo')->store('cvs', 'public')
                    : null;

                Curriculum::create([
                    'persona_id' => $persona->id,
                    'especialidad' => null, // Opcional, o deducido de la profesión
                    'archivo_cv' => $cvPath,
                ]);

                SolicitudEmpleo::create([
                    'persona_id' => $persona->id,
                    'estado' => 'pendiente',
                ]);

            } else { // Público General (estudiante_externo)
                $role = Role::where('nombre', 'publico')->first();
            }

            // Asignar el Rol de Acceso
            if ($role) {
                $persona->roles()->attach($role->id, [
                    'asignado_en' => now(),
                    'activo' => true,
                ]);
            }

            DB::commit();

            // Iniciar sesión automáticamente
            Auth::login($usuario);

            return redirect($this->redirectToDashboardByRole($usuario));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Ocurrió un error en el registro: ' . $e->getMessage()]);
        }
    }
}