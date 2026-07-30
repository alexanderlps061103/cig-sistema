<?php

use Illuminate\Support\Facades\Route;

// Controllers principales
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleActionController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicoController;      // <-- Importación del controlador público general
use App\Http\Controllers\EstudianteController;   // <-- Importación del controlador del estudiante
use App\Http\Controllers\RectorController;
use App\Http\Controllers\PlanificacionController;
use App\Http\Controllers\TrimestreController;
use App\Http\Controllers\DocenteController; 
use App\Http\Controllers\TemaController;        // Resolviendo BindingResolutionException
use App\Http\Controllers\DocumentoController;   // Resolviendo enrutamiento de generación de documentos
use App\Http\Controllers\DocenteInvitadoController; // Importación del controlador de invitados de manera pública
use App\Http\Controllers\{
    CoordinadorController,
    ConfiguracionController,
    ActividadController,
    CoordinadorUsuarioController,
    OperacionController,
    EstructuraController 
};

// --- AUTENTICACIÓN (cargada desde routes/auth.php) ---
require __DIR__.'/auth.php';

// --- RUTAS PÚBLICAS (accesibles para invitados sin iniciar sesión) ---
Route::get('/', [PublicController::class, 'index'])->name('home');

// Registro de Docentes Invitados (Público: Enlace compartido por la coordinadora)
Route::get('/registro-docente-invitado', [DocenteInvitadoController::class, 'create'])->name('docentes.invitado.create');
Route::post('/registro-docente-invitado', [DocenteInvitadoController::class, 'store'])->name('docentes.invitado.store');


// --- RUTAS PROTEGIDAS (requieren autenticación) ---
Route::middleware(['auth'])->group(function () {

    // Dashboard genérico (redirige dinámicamente según el rol activo del usuario)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Cambio de rol / upgrade
    Route::get('/switch-role/{role}', [RoleActionController::class, 'switchRole'])->name('role.switch');
    Route::post('/upgrade-estudiante', [RoleActionController::class, 'upgradeAEstudiante'])->name('estudiante.upgrade');

    // --- SOPORTE COMPATIBILIDAD JAVASCRIPT ---
    Route::prefix('diseno')->group(function () {
        Route::put('/{modulo}/{id}', [EstructuraController::class, 'update'])->name('diseno.update');
        Route::delete('/{modulo}/{id}', [EstructuraController::class, 'destroy'])->name('diseno.destroy');
    });

    // --- ROL ESTUDIANTE REGULAR ---
    Route::middleware(['auth', 'role:estudiante'])->prefix('estudiante')->name('estudiante.')->group(function () {
        Route::get('/dashboard', [EstudianteController::class, 'index'])->name('dashboard');
        Route::get('/scan/{sesion}', [EstudianteController::class, 'scan'])->name('scan');
        Route::post('/actividades/{actividad}/inscribirse', [EstudianteController::class, 'inscribirse'])->name('inscribirse');

        // PLACEHOLDERS: Rutas requeridas por la barra lateral del estudiante
        Route::get('/inscripciones/activas', [EstudianteController::class, 'index'])->name('inscripciones.activas');
        Route::get('/inscripciones/historial', [EstudianteController::class, 'index'])->name('inscripciones.historial');
        Route::get('/expediente', [EstudianteController::class, 'index'])->name('expediente');
        Route::get('/actividades/explorar', [EstudianteController::class, 'index'])->name('actividades.explorar');
    });

    // --- ROL DOCENTE ---
    Route::middleware(['auth', 'role:docente'])->prefix('docente')->name('docente.')->group(function () {
        // Dashboard principal
        Route::get('/dashboard', [DocenteController::class, 'index'])->name('dashboard');
        
        // Exportación de inscritos (Excel)
        Route::get('/actividades/{actividad}/exportar-inscritos', [DocenteController::class, 'exportInscritos'])->name('actividades.exportar-inscritos');
        
        // Listado de inscritos (AJAX / Tabla)
        Route::get('/actividades/{actividad}/inscritos-tabla', [DocenteController::class, 'inscritosTable'])->name('actividades.inscritos-tabla');
        
        // Tomar asistencia manual a un tema
        Route::post('/temas/{tema}/asistencia-manual', [DocenteController::class, 'tomarAsistenciaManual'])->name('temas.asistencia-manual');
    });

    // --- ROL RECTOR ---
    Route::middleware(['auth', 'role:rector'])->prefix('rector')->name('rector.')->group(function () {
        // Dashboard principal
        Route::get('/dashboard', [RectorController::class, 'dashboard'])->name('dashboard');

        // Listado y creación
        Route::get('/usuarios', [RectorController::class, 'usuariosIndex'])->name('usuarios.index');
        Route::post('/usuarios', [RectorController::class, 'usuariosStore'])->name('usuarios.store');

        // Actualización (Previene el error 404)
        Route::put('/usuarios/{id}', [RectorController::class, 'usuariosUpdate'])->name('usuarios.update');

        // Cambio de estado
        Route::patch('/usuarios/{id}/toggle', [RectorController::class, 'toggleStatus'])->name('usuarios.toggle');

        // CRUD Cargos
        Route::get('/cargos', [RectorController::class, 'cargosIndex'])->name('cargos.index');
        Route::post('/cargos', [RectorController::class, 'cargosStore'])->name('cargos.store');
        Route::put('/cargos/{id}', [RectorController::class, 'cargosUpdate'])->name('cargos.update');
        Route::patch('/cargos/{id}/toggle', [RectorController::class, 'cargosToggle'])->name('cargos.toggle');

        // CRUD Profesiones
        Route::get('/profesiones', [RectorController::class, 'profesionesIndex'])->name('profesiones.index');
        Route::post('/profesiones/store', [RectorController::class, 'profesionesStore'])->name('profesiones.store');
        Route::put('/profesiones/{id}', [RectorController::class, 'profesionesUpdate'])->name('profesiones.update');
        Route::patch('/profesiones/{id}/toggle', [RectorController::class, 'profesionesToggle'])->name('profesiones.toggle');

        // Solicitudes de Empleo
        Route::get('/solicitudes', [RectorController::class, 'solicitudesIndex'])->name('solicitudes.index');
        Route::post('/solicitudes/{id}/procesar', [RectorController::class, 'procesarSolicitud'])->name('solicitudes.procesar');

        // Docentes y Especialidades
        Route::get('/docentes', [RectorController::class, 'docentesIndex'])->name('docentes.index');

        // Reportes Estadísticos y Exportación
        Route::get('/reportes/actividades', [RectorController::class, 'reporteActividades'])->name('reportes.actividades');
        Route::get('/reportes/empleo', [RectorController::class, 'reporteEmpleo'])->name('reportes.empleo');
        Route::get('/exportar/actividades-pdf', [RectorController::class, 'exportarActividadesPDF'])->name('exportar.actividades.pdf');
        Route::get('/exportar/docentes-excel', [RectorController::class, 'exportarDocentesExcel'])->name('exportar.docentes.excel');

        // Apartado "Rector como Docente"
        Route::get('/mis-sesiones', [RectorController::class, 'misSesiones'])->name('mis_sesiones');
        Route::get('/mis-certificados', [RectorController::class, 'misCertificados'])->name('mis_certificados');

        // Inducción a Pasantías
        Route::get('/pasantias/revision', [RectorController::class, 'pasantiasIndex'])->name('pasantias.index');

        // CRUD Carreras
        Route::get('/carreras', [RectorController::class, 'carrerasIndex'])->name('carreras.index');
        Route::post('/carreras', [RectorController::class, 'carrerasStore'])->name('carreras.store');
        Route::put('/carreras/{id}', [RectorController::class, 'carrerasUpdate'])->name('carreras.update');
        Route::patch('/carreras/{id}/toggle', [RectorController::class, 'carrerasToggle'])->name('carreras.toggle');

        // CRUD Tipo de Estudiantes
        Route::get('/tipo-estudiantes', [RectorController::class, 'tipoEstudiantesIndex'])->name('tipo_estudiantes.index');
        Route::post('/tipo-estudiantes', [RectorController::class, 'tipoEstudiantesStore'])->name('tipo_estudiantes.store');
        Route::put('/tipo-estudiantes/{id}', [RectorController::class, 'tipoEstudiantesUpdate'])->name('tipo_estudiantes.update');
        Route::patch('/tipo-estudiantes/{id}/toggle', [RectorController::class, 'tipoEstudiantesToggle'])->name('tipo_estudiantes.toggle');
    });

    // --- ROL COORDINADOR ---
    Route::middleware(['auth', 'role:coordinador'])->prefix('coordinador')->name('coordinador.')->group(function () {
        Route::get('/dashboard', [CoordinadorController::class, 'dashboard'])->name('dashboard');
        
        // RUTA INCORPORADA: Listado de Actividades e Inscritos para el menú
        Route::get('/actividades-listado', [CoordinadorController::class, 'listarActividadesConInscritos'])->name('actividades.listado');

        Route::prefix('planificacion')->name('planificacion.')->group(function () {
            Route::get('/', [PlanificacionController::class, 'index'])->name('index');
            Route::post('/store', [PlanificacionController::class, 'store'])->name('store');
            Route::put('/{id}/update', [PlanificacionController::class, 'update'])->name('update');
            Route::delete('/{id}/destroy', [PlanificacionController::class, 'destroy'])->name('destroy');
            Route::get('/trimestres', [TrimestreController::class, 'index'])->name('trimestres.index');
            Route::post('/trimestres/store', [TrimestreController::class, 'store'])->name('trimestres.store');
            Route::put('/trimestres/{id}/update', [TrimestreController::class, 'update'])->name('trimestres.update');
            Route::delete('/trimestres/{id}/destroy', [TrimestreController::class, 'destroy'])->name('trimestres.destroy');
        });

        Route::prefix('entidades_crud')->name('entidades_crud.')->group(function () {
            Route::get('/{modulo}', [EstructuraController::class, 'index'])->name('index');
            Route::post('/{modulo}', [EstructuraController::class, 'store'])->name('store');
            Route::put('/{modulo}/{id}', [EstructuraController::class, 'update'])->name('update');
            Route::delete('/{modulo}/{id}', [EstructuraController::class, 'destroy'])->name('destroy');
        });

        // Gestión de Actividades
        Route::resource('actividades', ActividadController::class);

        // Gestión de Sesiones (Temas)
        Route::prefix('sesiones')->name('sesiones.')->group(function () {
            Route::post('/', [TemaController::class, 'store'])->name('store');
            Route::put('/{id}', [TemaController::class, 'update'])->name('update');
            Route::delete('/{id}', [TemaController::class, 'destroy'])->name('destroy');
        });

        Route::get('/documento/generar/{id_inscripcion}', [DocumentoController::class, 'generarDocumento'])->name('documento.generar');
        Route::get('/mis-sesiones', [CoordinadorController::class, 'misSesiones'])->name('mis_sesiones');
        Route::get('/mis-certificados', [CoordinadorController::class, 'misCertificados'])->name('mis_certificados');
    });

    // --- ROL PÚBLICO GENERAL / ESTUDIANTE NO REGULAR ---
    Route::middleware(['auth', 'role:publico'])->prefix('publico')->name('publico.')->group(function () {
        // Dashboard principal del público general
        Route::get('/dashboard', [PublicoController::class, 'dashboard'])->name('dashboard');
        
        // CORREGIDO: Ajustado el endpoint para coincidir con la URL del Frontend (/publico/inscribir/{actividad})
        Route::post('/inscribir/{actividad}', [PublicoController::class, 'inscribir'])->name('actividades.inscribir');

        // Rutas placeholder para el menú lateral
        Route::get('/actividades', [PublicoController::class, 'actividadesIndex'])->name('actividades.index');
        Route::get('/certificados', [PublicoController::class, 'certificadosIndex'])->name('certificados');
        Route::get('/encuestas', [PublicoController::class, 'encuestasIndex'])->name('encuestas');
        Route::get('/solicitar-estudiante', [PublicoController::class, 'solicitarEstudiante'])->name('solicitar.estudiante');
    });
});