<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Persona, Usuario, Role, Actividad, Curriculum,
 SolicitudEmpleo, Docente, Tema, Certificado, SolicitudInduccion,
 Cargo, Profesion, TipoEstudiante, Estudiante, ExpedienteEstudiante, Carrera, Empleado};
use Illuminate\Support\Facades\{DB, Hash, Validator};
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AsistenciasExport;

class RectorController extends Controller
{
    /**
     * Dashboard con KPIs (Estadísticas reales)
     */
    public function dashboard()
    {
        $totalUsuarios = Usuario::count();
        $solicitudesPendientes = SolicitudEmpleo::where('estado', 'pendiente')->count();
        $totalCoordinadores = Persona::whereHas('roles', fn($q) => $q->where('nombre', 'coordinador'))->count();

        $ultimosLogs = DB::table('persona_rol')
            ->join('personas', 'persona_rol.persona_id', '=', 'personas.id')
            ->join('roles', 'persona_rol.rol_id', '=', 'roles.id')
            ->select('persona_rol.created_at', 'personas.nombres', 'personas.apellidos', 'roles.nombre as nombre_rol')
            ->latest('persona_rol.created_at')->take(5)->get()
            ->map(function($log) {
                $log->accion = "Rol '" . ucfirst($log->nombre_rol) . "' asignado a {$log->nombres} {$log->apellidos}";
                $log->created_at = \Carbon\Carbon::parse($log->created_at);
                return $log;
            });

        // Cargamos la relación tema con su docente, el tipo de actividad y el salón asignado
        $actividades = Actividad::with(['tema.docente.persona', 'tipo', 'salon'])
            ->where('fecha', '>=', now())
            ->orderBy('fecha', 'asc')->take(5)->get();

        $actividadesMapped = $actividades->map(function($act) {
            // Formatear el horario de la actividad usando su propia fecha y hora
            $horario = '-';
            if ($act->fecha) {
                $fechaFormateada = $act->fecha->format('d/m/Y');
                $horaFormateada = $act->hora_inicio
                    ? date('H:i', strtotime($act->hora_inicio))
                    : '';
                $horario = trim("$fechaFormateada $horaFormateada");
            }

            // Obtener el docente asignado a través del tema
            $docenteNombre = 'N/A';
            if ($act->tema && $act->tema->docente) {
                $docenteObj = $act->tema->docente;
                $nombres = $docenteObj->nombres ?? ($docenteObj->persona->nombres ?? '');
                $apellidos = $docenteObj->apellidos ?? ($docenteObj->persona->apellidos ?? '');
                $docenteNombre = trim("$nombres $apellidos") ?: 'N/A';
            }

            return [
                'id' => $act->id_actividad, // Clave primaria real del nuevo modelo Actividad
                'titulo' => $act->nombre,
                'horario' => $horario,
                'docente' => $docenteNombre,
                'salon' => $act->salon ? ($act->salon->nombre ?? $act->salon->descripcion ?? 'Definido') : 'No definido',
                'capacidad' => $act->cupos ?? 'N/A',
            ];
        })->keyBy('id');

        return view('rector.dashboardRector', compact('totalUsuarios', 'solicitudesPendientes', 'totalCoordinadores', 'ultimosLogs', 'actividades', 'actividadesMapped'));
    }

    // ==========================================
    // 2. Gestionar Usuarios (CRUD)
    // ==========================================
    public function usuariosIndex(Request $request)
    {
        $buscar = $request->get('buscar');
        $rolFiltro = $request->get('rol');

        $usuarios = Persona::with(['usuario', 'roles', 'estudiante.carrera', 'docentes.profesion', 'empleado.cargo'])
            ->when($buscar, function($q) use ($buscar) {
                $q->where('cedula', 'like', "%$buscar%")->orWhere('nombres', 'like', "%$buscar%");
            })
            ->when($rolFiltro, function($q) use ($rolFiltro) {
                $q->whereHas('roles', fn($query) => $query->where('nombre', $rolFiltro));
            })
            ->paginate(15)->withQueryString();

        return view('rector.usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => Role::all(),
            'carreras' => Carrera::where('estado', 1)->get(),
            'cargos' => Cargo::all(),
            'profesiones' => Profesion::all()
        ]);
    }

    public function usuariosStore(Request $request) {
        return $this->saveUser($request);
    }

    public function usuariosUpdate(Request $request, $id) {
        return $this->saveUser($request, $id);
    }

    private function saveUser($request, $id = null) {
        DB::beginTransaction();
        try {
            if ($id) {
                $persona = Persona::findOrFail($id);
                $usuario = $persona->usuario;
            } else {
                $persona = new Persona;
                $usuario = new Usuario;
                $persona->cedula = $request->cedula;
            }

            // Datos de Persona
            $persona->nombres = $request->nombres;
            $persona->apellidos = $request->apellidos;
            $persona->telefono = $request->telefono;
            $persona->sexo = $request->sexo;
            $persona->save();

            // Datos de Usuario
            $usuario->persona_id = $persona->id;
            $usuario->email = $request->email;
            $usuario->activo = 1;
            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }
            $usuario->save();

            // Roles duales y aditivos
            if ($request->filled('rol_id')) {
                $persona->roles()->syncWithoutDetaching([
                    $request->rol_id => [
                        'activo' => 1,
                        'asignado_en' => now(),
                        'asignado_por' => auth()->id()
                    ]
                ]);
            }

            // Perfiles asociados
            $rol = Role::find($request->rol_id);
            $rolNombre = strtolower($rol->nombre);

            if ($rolNombre == 'estudiante') {
                Estudiante::updateOrCreate(['persona_id' => $persona->id], ['carrera_id' => $request->carrera_id]);
            } elseif (in_array($rolNombre, ['docente', 'coordinador', 'rector'])) {
                Docente::updateOrCreate(['persona_id' => $persona->id], ['profesion_id' => $request->profesion_id]);
                Empleado::updateOrCreate(['persona_id' => $persona->id], ['cargo_id' => $request->cargo_id]);
            }

            DB::commit();
            return back()->with('success', 'Operación exitosa.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id) {
        $u = Usuario::where('persona_id', $id)->firstOrFail();
        $u->activo = !$u->activo;
        $u->save();
        return back()->with('success', 'Estado cambiado.');
    }

    /**
     * Procesar Solicitud de Empleo (Autorización del Rector)
     */
    public function procesarSolicitud(Request $request, $id)
{
    $request->validate(['estado' => 'required|in:aprobada,rechazada']);

    $solicitud = SolicitudEmpleo::findOrFail($id);
    $persona = $solicitud->persona;

    DB::beginTransaction();
    try {
        // 1. Actualizar el estado de la solicitud
        $solicitud->update([
            'estado' => $request->estado,
            'revisado_por' => auth()->user()->persona_id,
            'fecha_revision' => now(),
        ]);

        if ($request->estado === 'aprobada') {
            // --- CONFIGURACIÓN DE APROBACIÓN ---

            // A) Buscar la profesión que eligió al registrarse (en su currículum)
            $curriculum = Curriculum::where('persona_id', $persona->id)->first();
            $profesionId = $curriculum ? $curriculum->profesion_id : null;

            // B) Crear o Actualizar perfil de Docente con SU profesión real
            Docente::updateOrCreate(
                ['persona_id' => $persona->id],
                ['profesion_id' => $profesionId]
            );

            // C) Crear o Actualizar perfil de Empleado con cargo de "Docente"
            // Buscamos el ID del cargo que se llame "Docente"
            $cargoDocente = Cargo::where('nombre', 'LIKE', '%Docente%')->first();

            Empleado::updateOrCreate(
                ['persona_id' => $persona->id],
                ['cargo_id' => $cargoDocente ? $cargoDocente->id : null]
            );

            // D) Cambio de Roles: Quitar "Publico", Poner "Docente"
            $rolDocente = Role::where('nombre', 'docente')->first();
            $rolPublico = Role::where('nombre', 'publico')->first();

            if ($rolPublico) {
                $persona->roles()->detach($rolPublico->id);
            }

            if ($rolDocente) {
                $persona->roles()->syncWithoutDetaching([
                    $rolDocente->id => [
                        'activo' => true,
                        'asignado_en' => now(),
                        'asignado_por' => auth()->user()->persona_id
                    ]
                ]);
            }

            $mensaje = "Solicitud aprobada: {$persona->nombres} ahora es Docente con cargo y rol actualizado.";
        } else {
            $mensaje = "La solicitud ha sido rechazada.";
        }

        DB::commit();
        return back()->with('success', $mensaje);
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}

    /**
     * Estadísticas de Empleo (Gráficas)
     */
    public function reporteEmpleo()
    {
        $statsEstado = [
            'pendientes' => SolicitudEmpleo::where('estado', 'pendiente')->count(),
            'aprobadas'  => SolicitudEmpleo::where('estado', 'aprobada')->count(),
            'rechazadas' => SolicitudEmpleo::where('estado', 'rechazada')->count(),
        ];

        $porProfesion = DB::table('solicitudes_empleo')
            ->join('personas', 'solicitudes_empleo.persona_id', '=', 'personas.id')
            ->leftJoin('docentes', 'personas.id', '=', 'docentes.persona_id')
            ->leftJoin('profesiones', 'docentes.profesion_id', '=', 'profesiones.id')
            ->select('profesiones.nombre', DB::raw('count(solicitudes_empleo.id) as total'))
            ->groupBy('profesiones.nombre')
            ->get();

        return view('rector.reportes.empleo', compact('statsEstado', 'porProfesion'));
    }

    // ==========================================
    // 3. GESTIÓN DE CARGOS (CRUD Completo)
    // ==========================================
    public function cargosIndex(Request $request)
    {
        // Usamos paginate para que la tabla sea manejable
        $cargos = \App\Models\Cargo::paginate(10);
        return view('rector.cargos.index', compact('cargos'));
    }

    public function cargosStore(Request $request)
    {
        $request->validate(['nombre' => 'required|string|unique:cargos,nombre']);
        \App\Models\Cargo::create(['nombre' => $request->nombre]);
        return back()->with('success', 'Cargo creado exitosamente.');
    }

    public function cargosUpdate(Request $request, $id)
    {
        $cargo = \App\Models\Cargo::findOrFail($id);
        $request->validate(['nombre' => 'required|string|unique:cargos,nombre,'.$id]);
        $cargo->update(['nombre' => $request->nombre]);
        return back()->with('success', 'Cargo actualizado.');
    }

    public function cargosToggle($id)
    {
        $cargo = \App\Models\Cargo::findOrFail($id);

        // Cambiamos el estado: si es 1 pasa a 0, si es 0 pasa a 1
        $cargo->estado = !$cargo->estado;
        $cargo->save();

        $mensaje = $cargo->estado ? 'Cargo habilitado exitosamente.' : 'Cargo inhabilitado correctamente.';

        return back()->with('success', $mensaje);
    }

    // ==========================================
    // 4. GESTIÓN DE PROFESIONES (CRUD Completo)
    // ==========================================
    public function profesionesIndex(Request $request)
    {
        $registros = \App\Models\Profesion::paginate(10);
        return view('rector.profesiones.index', compact('registros'));
    }

    public function profesionesStore(Request $request)
    {
        $request->validate(['nombre' => 'required|string|unique:profesiones,nombre']);
        \App\Models\Profesion::create($request->all());
        return back()->with('success', 'Profesión registrada correctamente.');
    }

    public function profesionesUpdate(Request $request, $id)
    {
        $profesion = \App\Models\Profesion::findOrFail($id);
        $request->validate(['nombre' => 'required|string|unique:profesiones,nombre,'.$id]);
        $profesion->update($request->all());
        return back()->with('success', 'Profesión actualizada con éxito.');
    }

    public function profesionesToggle($id)
    {
        $profesion = \App\Models\Profesion::findOrFail($id);
        // Usamos la columna 'estado' del modelo
        $profesion->estado = !$profesion->estado;
        $profesion->save();

        return back()->with('success', 'Estado de la profesión actualizado.');
    }

    /**
     * REPORTE: Actividades por Coordinador y Frecuencia
     */
    public function reporteActividades()
    {
        // 1. Actividades por cada coordinador (Estadística para las barras)
        $reporteCoordinadores = Persona::whereHas('roles', fn($q) => $q->where('nombre', 'coordinador'))
            ->withCount('documentosExpediente')
            ->get();

        // 2. Frecuencia de tipos de actividad (Corregido con la tabla 'tipo_actividades' y su llave primaria)
        $frecuenciaActividades = DB::table('actividades')
            ->join('tipo_actividades', 'actividades.id_tipo_actividad', '=', 'tipo_actividades.id_tipo_actividad')
            ->select('tipo_actividades.nombre', DB::raw('count(*) as total'))
            ->groupBy('tipo_actividades.nombre')
            ->get();

        // 3. LISTADO DE ACTIVIDADES (Adaptado: quitamos creador y cargamos la relación 'tema.docente')
        $actividades = Actividad::with(['tipo', 'tema.docente'])
            ->latest()
            ->take(20)
            ->get();

        // Enviamos las 3 variables a la vista
        return view('rector.reportes.actividades', compact(
            'reporteCoordinadores',
            'frecuenciaActividades',
            'actividades'
        ));
    }

    /**
     * REPORTE: Docentes más frecuentes (basado en ponencias)
     */
    public function docentesIndex(Request $request)
    {
        $docentesMasUsados = Docente::with('persona', 'profesion')
            ->withCount('ponencias')
            ->orderBy('ponencias_count', 'desc')
            ->paginate(15);

        return view('rector.docentes.index', compact('docentesMasUsados'));
    }

    /**
     * Exportación a PDF de las actividades del mes
     */
    public function exportarActividadesPDF()
    {
        // Corregido: cambiamos 'modalidad' por 'modalidadRelacion' y removemos 'creador' para evitar fallos
        $actividades = Actividad::with(['tipo', 'modalidadRelacion'])->get();
        $pdf = Pdf::loadView('rector.reportes.pdf_actividades', compact('actividades'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('reporte_actividades_general.pdf');
    }

    /**
     * RECTOR COMO DOCENTE: Ver sus propios temas/clases
     */
    public function misSesiones()
    {
        $personaId = auth()->user()->persona_id;

        // Buscamos el perfil de docente de la persona logueada
        $docente = Docente::where('persona_id', $personaId)->first();

        // Buscamos los temas asociados a este docente (para no romper la vista se compacta como "sesiones")
        $sesiones = $docente
            ? Tema::where('id_docente', $docente->id)->get()
            : collect();

        return view('rector.mis_sesiones', compact('sesiones'));
    }

    /**
     * RECTOR COMO DOCENTE: Sus certificados recibidos
     */
    public function misCertificados()
    {
        $certificados = Certificado::where('persona_id', auth()->user()->persona_id)
            ->with('actividad')
            ->get();

        return view('rector.mis_certificados', compact('certificados'));
    }

    /**
     * Revisión de Estudiantes para Pasantías
     */
    public function pasantiasIndex()
    {
        $solicitudes = SolicitudInduccion::with(['estudiante.persona', 'estudiante.carrera'])
            ->latest()
            ->paginate(20);

        return view('rector.pasantias.index', compact('solicitudes'));
    }

    public function solicitudesIndex(Request $request)
    {
        $estado = $request->get('estado');

        $solicitudes = \App\Models\SolicitudEmpleo::with([
            'persona.usuario',
            'persona.curriculum', // Eager loading correcto
            'persona.docentes.profesion'
        ])
        ->when($estado, function ($query, $estado) {
            return $query->where('estado', $estado);
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('rector.solicitudes.index', compact('solicitudes'));
    }

    // ==========================================
    // 8. GESTIÓN DE TIPOS DE ESTUDIANTE (CRUD)
    // ==========================================
    public function tipoEstudiantesIndex()
    {
        // Usamos $registros para que coincida con la vista
        $registros = \App\Models\TipoEstudiante::paginate(10);
        return view('rector.tipo_estudiantes.index', compact('registros'));
    }

    public function tipoEstudiantesStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:tipo_estudiantes,nombre'
        ]);

        \App\Models\TipoEstudiante::create([
            'nombre' => $request->nombre,
            'estado' => true
        ]);

        return back()->with('success', 'Registro guardado exitosamente.');
    }

    public function tipoEstudiantesUpdate(Request $request, $id)
    {
        $tipo = \App\Models\TipoEstudiante::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|unique:tipo_estudiantes,nombre,'.$id
        ]);

        $tipo->update(['nombre' => $request->nombre]);
        return back()->with('success', 'Registro actualizado correctamente.');
    }

    public function tipoEstudiantesToggle($id)
    {
        $tipo = \App\Models\TipoEstudiante::findOrFail($id);
        $tipo->update(['estado' => !$tipo->estado]);
        return back()->with('success', 'Estado actualizado.');
    }

    // ==========================================
    // 9. GESTIÓN DE CARRERAS (CRUD Completo)
    // ==========================================
    public function carrerasIndex()
    {
        $registros = \App\Models\Carrera::paginate(10);
        return view('rector.carreras.index', compact('registros'));
    }

    public function carrerasStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:carreras,nombre',
            'descripcion' => 'nullable|string'
        ]);

        \App\Models\Carrera::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado' => true
        ]);

        return back()->with('success', 'Carrera creada exitosamente.');
    }

    public function carrerasUpdate(Request $request, $id)
    {
        $carrera = \App\Models\Carrera::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|unique:carreras,nombre,'.$id,
            'descripcion' => 'nullable|string'
        ]);

        $carrera->update($request->all());
        return back()->with('success', 'Carrera actualizada correctamente.');
    }

    public function carrerasToggle($id)
    {
        $carrera = \App\Models\Carrera::findOrFail($id);

        // Cambiamos el estado (si es true pasa a false y viceversa)
        $carrera->estado = !$carrera->estado;
        $carrera->save();

        $status = $carrera->estado ? 'habilitada' : 'inhabilitada';
        return back()->with('success', "La carrera ha sido $status correctamente.");
    }
}
