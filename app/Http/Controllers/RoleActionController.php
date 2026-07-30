<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\ExpedienteEstudiante;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\RedirectsByRole;

class RoleActionController extends Controller
{
    use RedirectsByRole;

    /**
     * Alternar de rol activo en sesión (ej. de Coordinadora a Docente y viceversa)
     */
    public function switchRole($role)
    {
        $user = Auth::user();
        $roleClean = strtolower($role);
        
        // Extraemos los roles asignados que posee la persona asociada
        $rolesUsuario = $user->persona->roles->pluck('nombre')->map(fn($r) => strtolower($r))->toArray();

        if (in_array($roleClean, $rolesUsuario)) {
            // Guardamos el nuevo rol seleccionado en sesión
            session(['role_active' => $roleClean]);

            // Redirigimos usando el trait "inteligente" para que resuelva la ruta del dashboard correspondiente
            return redirect($this->redirectToDashboardByRole($user))
                ->with('success', 'Has cambiado a la vista de ' . ucfirst($roleClean) . ' correctamente.');
        }

        return back()->with('error', 'No tienes asignado este rol en tu perfil.');
    }

    /**
     * Pasar de Público a Estudiante Regular (Subida de documentos y expediente)
     */
    public function upgradeAEstudiante(Request $request)
    {
        $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'foto_carnet' => 'required|image|max:2048',
            'constancia_aprobacion' => 'required|mimes:pdf|max:5120',
        ]);

        $persona = Auth::user()->persona;

        DB::beginTransaction();
        try {
            // 1. Crear el perfil de Estudiante asociado a la persona
            $estudiante = Estudiante::create([
                'persona_id' => $persona->id,
                'carrera_id' => $request->carrera_id,
                'es_regular' => true,
            ]);

            // 2. Guardar el archivo físico y estructurar el expediente
            $path = $request->file('foto_carnet')->store('expedientes/carnets', 'public');
            ExpedienteEstudiante::create([
                'estudiante_id' => $estudiante->id,
                'carrera_id' => $request->carrera_id,
                'ruta_carnet' => $path,
            ]);

            // 3. Vincular el rol oficial de 'estudiante' sin desconectar otros posibles roles
            $rolEst = Role::where('nombre', 'estudiante')->first();
            if ($rolEst) {
                $persona->roles()->syncWithoutDetaching([
                    $rolEst->id => [
                        'activo' => true, 
                        'asignado_en' => now()
                    ]
                ]);
            }

            // 4. Cambiar el rol activo en sesión
            session(['role_active' => 'estudiante']);
            
            DB::commit();

            return redirect()->route('estudiante.dashboard')->with('success', '¡Felicidades! Ahora eres estudiante regular.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un inconveniente al procesar la solicitud.');
        }
    }
}