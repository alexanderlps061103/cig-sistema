@extends('layouts.app')

@section('title', 'Panel de Docencia')

@push('styles')
    <!-- Estilo Específico del Panel del Docente -->
    <link rel="stylesheet" href="{{ asset('assets/css/docente/dashboardDocente.css') }}">
@endpush

@section('content')
<div class="dashboard-scroll-container">

    <!-- Cabecera de Contenido -->
    <header class="main-header">
        <div class="header-welcome">
            <h1>Panel de Docencia</h1>
            <p>Bienvenido, Profesor. Control de clases, sesiones y verificación de asistencia.</p>
        </div>
        <div class="header-date">
            <i class="fa-regular fa-calendar"></i>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('d F, Y') }}</span>
        </div>
    </header>

    {{-- KPIs --}}
    <section class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon icon-blue"><i class="fa-solid fa-graduation-cap"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Clases Asignadas</span>
                <span class="kpi-value">{{ $sesiones->count() }} Sesiones</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-green"><i class="fa-solid fa-clock"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Horas Dictadas</span>
                <span class="kpi-value">18 Horas</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-purple"><i class="fa-solid fa-user-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Asistencia Promedio</span>
                <span class="kpi-value">88% Alumnos</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-orange"><i class="fa-solid fa-circle-exclamation"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Pendientes por Cerrar</span>
                <span class="kpi-value">2 Sesiones</span>
            </div>
        </div>
    </section>

    <!-- Alertas Flotantes -->
    <div class="alerts-container">
        @if(session('success'))
            <div class="alert-message success-alert">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="alert-message info-alert" style="background-color: var(--color-bg-info, #e0f2fe); color: var(--color-info, #0369a1); border-left: 0.4rem solid #0ea5e9; padding: 1.2rem 1.6rem; display: flex; align-items: center; gap: 1rem; border-radius: 0.8rem; box-shadow: var(--shadow-sm); font-size: 1.25rem;">
                <i class="fa-solid fa-circle-info"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-message error-alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <ul style="margin: 0; padding-left: 1rem; list-style-type: none;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Barra de Acciones del Docente -->
    <div class="action-header-bar">
        <button class="btn-create-activity" id="btn-scan-qr">
            <i class="fa-solid fa-camera"></i> Escanear Código QR
        </button>
        <button class="btn-create-session" id="btn-download-report">
            <i class="fa-solid fa-file-excel"></i> Descargar Reporte Alumnos
        </button>
    </div>

    <!-- Área Central de Trabajo (Control de Sesiones + Filtros) -->
    <section class="dashboard-grid" style="margin-top: 2rem;">
        
        <!-- Listado de Sesiones Dictadas (Control de Clases) -->
        <div class="calendar-card">
            <div class="calendar-header">
                <h2>Mis Sesiones y Clases Programadas</h2>
                <div class="calendar-navigation">
                    <span class="current-month-year">Trimestre Activo: 2026-III</span>
                </div>
            </div>

            <!-- Grilla / Tabla de Sesiones Activas -->
            <div style="overflow-x: auto; margin-top: 1.5rem;">
                <table style="width: 100%; border-collapse: collapse; font-size: 1.25rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 0.2rem solid var(--color-border); color: var(--color-text-muted);">
                            <th style="padding: 1.2rem 0.8rem;">Sesión N°</th>
                            <th style="padding: 1.2rem 0.8rem;">Tema / Actividad</th>
                            <th style="padding: 1.2rem 0.8rem;">Fecha / Hora</th>
                            <th style="padding: 1.2rem 0.8rem;">Aula</th>
                            <th style="padding: 1.2rem 0.8rem; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesiones as $index => $actividad)
                            <tr style="border-bottom: 0.1rem solid var(--color-border);">
                                <td style="padding: 1.6rem 0.8rem; font-weight: 700;">Clase {{ sprintf('%02d', $index + 1) }}</td>
                                <td style="padding: 1.6rem 0.8rem;">
                                    <strong>{{ $actividad->nombre }}</strong><br>
                                    <small style="color: var(--color-text-muted);">
                                        Tema: {{ $actividad->tema->nombre ?? 'Sin tema asignado' }}
                                    </small>
                                </td>
                                <td style="padding: 1.6rem 0.8rem;">
                                    {{ $actividad->fecha ? $actividad->fecha->translatedFormat('l d M') : 'N/A' }}<br>
                                    <small style="color: var(--color-text-muted);">
                                        {{ $actividad->hora_inicio ? date('h:i A', strtotime($actividad->hora_inicio)) : '' }} - 
                                        {{ $actividad->hora_fin ? date('h:i A', strtotime($actividad->hora_fin)) : '' }}
                                    </small>
                                </td>
                                <td style="padding: 1.6rem 0.8rem;">
                                    {{ $actividad->salon->nombre ?? 'No definido' }}
                                </td>
                                <td style="padding: 1.6rem 0.8rem; text-align: center;">
                                    <form action="{{ route('docente.temas.asistencia-manual', $actividad->id_tema) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="button" class="action-btn btn-primary-action manage-session-btn" data-id="{{ $actividad->id_tema }}" style="padding: 0.6rem 1.2rem; font-size: 0.85rem; margin: 0 auto;">
                                            <i class="fa-solid fa-qrcode"></i> Pasar Asistencia
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--color-text-muted);">
                                    No tiene clases programadas para este trimestre.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bloque Lateral de Filtros e Indicaciones -->
        <aside class="filter-panel">
            <div class="panel-section">
                <h3>Filtros de Agenda</h3>
                <div class="filter-group">
                    <label for="filter-status">Estado de Sesión</label>
                    <select id="filter-status" class="form-select">
                        <option value="">Todas las sesiones</option>
                        <option value="pendiente">Pendientes por iniciar</option>
                        <option value="completada">Completadas</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filter-activity">Actividad Académica</label>
                    <select id="filter-activity" class="form-select">
                        <option value="">Todas las actividades</option>
                        <option value="1">Taller de Redes</option>
                        <option value="2">Foro de Liderazgo</option>
                    </select>
                </div>
            </div>
        </aside>
    </section>

</div>

{{-- ==================== MODALES ==================== --}}

{{-- 1. MODAL DE CONTROL DE ASISTENCIA Y EJECUCIÓN --}}
<div class="modal-overlay" id="activity-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="modal-title">Cargando Datos de Sesión...</h3>
            <button class="modal-close" id="close-modal-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-detail-row">
                <i class="fa-regular fa-clock"></i>
                <div>
                    <strong>Fecha y Horario:</strong>
                    <p id="modal-time">Viernes, 10:00 AM - 12:00 PM</p>
                </div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <strong>Aula / Espacio Asignado:</strong>
                    <p id="modal-location">Laboratorio de Redes (Planta Alta)</p>
                </div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-users-line"></i>
                <div>
                    <strong>Control de Alumnos:</strong>
                    <p id="modal-capacity">0 Presentes / 30 Inscritos</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn btn-primary" id="btn-attendance">
                <i class="fa-solid fa-qrcode"></i> Escanear Asistencia (QR)
            </button>
            <button class="modal-btn btn-secondary" id="btn-complete">
                <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Finalizar Sesión
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Scripts del Sistema -->
    <script src="{{ asset('assets/js/docente.js') }}"></script>
@endpush