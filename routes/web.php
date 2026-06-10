<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\CoordinadorController;
use Illuminate\Support\Facades\Route;

// Página de bienvenida (sin cambios)
Route::get('/', function () {
    return view('welcome');
});

// ================== AUTENTICACIÓN ==================
// Rutas de login/logout (sin verificación de email ni profile por ahora)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ================== RUTAS PROTEGIDAS ==================
Route::middleware(['auth'])->group(function () {

    // Selección de rol (después del login)
    Route::get('/seleccionar-rol', [DashboardController::class, 'selectRole'])->name('seleccionar-rol');
    Route::post('/seleccionar-rol', [DashboardController::class, 'setRole'])->name('set-role');

    // ---- ESTUDIANTE ----
    Route::middleware(['check.role:estudiante'])->prefix('estudiante')->name('estudiante.')->group(function () {
        Route::get('/dashboard', [EstudianteController::class, 'dashboard'])->name('dashboard');
        // Aquí irán las demás rutas del estudiante (inscripciones, certificados, pasantías...)
    });

    // ---- DOCENTE ----
    Route::middleware(['check.role:docente'])->prefix('docente')->name('docente.')->group(function () {
        Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('dashboard');
        // Rutas de docente: ver sesiones, tomar asistencia, certificados...
    });

    // ---- COORDINADOR (admin) ----
    Route::middleware(['check.role:coordinador'])->prefix('coordinador')->name('coordinador.')->group(function () {
        Route::get('/dashboard', [CoordinadorController::class, 'dashboard'])->name('dashboard');
        // CRUD de trimestres, actividades, sesiones, gestión de usuarios, etc.
    });

    // Ejemplo de ruta con middleware adicional (pasantías)
    Route::middleware(['check.role:estudiante', 'check.aprobado'])->prefix('estudiante/pasantias')->name('estudiante.pasantias.')->group(function () {
        Route::get('/', [EstudianteController::class, 'pasantias'])->name('index');
        // ...
    });
});
