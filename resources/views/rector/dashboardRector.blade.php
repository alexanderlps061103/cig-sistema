@extends('layouts.app')

@section('title', 'Panel Rector')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/dashboardRector.css') }}">
@endpush

@section('content')
<div class="dashboard-scroll-container">

    <header class="main-header">
        <div class="header-welcome">
            <h1>Panel de Rectoría</h1>
            <p>Bienvenido, {{ Auth::user()->persona ? Auth::user()->persona->nombres . ' ' . Auth::user()->persona->apellidos : Auth::user()->email }}</p>
        </div>
        <div class="header-date">
            <i class="fa-regular fa-calendar"></i>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('d F, Y') }}</span>
        </div>
    </header>

    {{-- KPIs --}}
    <section class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon icon-blue"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Usuarios Totales</span>
                <span class="kpi-value">{{ $totalUsuarios }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-green"><i class="fa-solid fa-briefcase"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Solicitudes Pendientes</span>
                <span class="kpi-value">{{ $solicitudesPendientes }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-purple"><i class="fa-solid fa-user-tie"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Coordinadores</span>
                <span class="kpi-value">{{ $totalCoordinadores }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-orange"><i class="fa-solid fa-file-lines"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Registros Recientes</span>
                <span class="kpi-value">{{ $ultimosLogs->count() }}</span>
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

    <section class="dashboard-grid">
        {{-- Actividades --}}
        <div class="calendar-card">
            <h3>Actividades Próximas</h3>
            <div style="margin-top:1rem;">
                @forelse($actividades as $actividad)
                    @php
                        $fechaHora = 'Sin fecha';
                        if ($actividad->fecha) {
                            $fechaFormateada = $actividad->fecha->format('d/m');
                            $horaFormateada = $actividad->hora_inicio 
                                ? date('H:i', strtotime($actividad->hora_inicio)) 
                                : '00:00';
                            $fechaHora = "$fechaFormateada $horaFormateada";
                        }
                        $tipoNombre = strtolower($actividad->tipo->nombre ?? 'taller');
                    @endphp
                    <span class="event-tag {{ $tipoNombre }}" data-id="{{ $actividad->id_actividad }}">
                        {{ $actividad->nombre }} ({{ $fechaHora }})
                    </span>
                @empty
                    <p class="text-muted">No hay actividades programadas.</p>
                @endforelse
            </div>
        </div>

        {{-- Logs --}}
        <aside class="log-panel">
            <div class="panel-section">
                <h3>Registros recientes</h3>
                @forelse($ultimosLogs as $log)
                    <div class="log-item">
                        <div style="flex:1">
                            <div class="log-title">{{ $log->accion }}</div>
                            <div class="meta">{{ $log->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <p>No hay registros recientes.</p>
                @endforelse
            </div>

            <div class="panel-section">
                <h3>Acciones rápidas</h3>
                <a class="action-btn btn-primary-action" href="{{ route('rector.usuarios.index') }}">
                    <i class="fa-solid fa-users"></i> Gestionar Usuarios
                </a>
                <a class="action-btn btn-secondary-action" href="{{ route('rector.docentes.index') }}">
                    <i class="fa-solid fa-chalkboard-user"></i> Ver Docentes
                </a>
            </div>
        </aside>
    </section>

</div>

{{-- ==================== MODALES ==================== --}}

{{-- 1. MODAL DETALLES (Adaptado a la estructura de filas con iconos de tu CSS) --}}
<div id="activity-modal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="modal-title">Detalle de la Actividad</h3>
            <button id="close-activity-modal" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-detail-row">
                <i class="fa-solid fa-clock"></i>
                <div>
                    <strong>Horario</strong>
                    <p id="modal-time">Cargando...</p>
                </div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-chalkboard-user"></i>
                <div>
                    <strong>Docente Responsable</strong>
                    <p id="modal-docent">Cargando...</p>
                </div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <strong>Lugar / Espacio</strong>
                    <p id="modal-location">Cargando...</p>
                </div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-users"></i>
                <div>
                    <strong>Cupos Disponibles</strong>
                    <p id="modal-capacity">Cargando...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Esto envía los datos del controlador al archivo JS de forma segura
        window.ACTIVIDADES = @json($actividadesMapped);
    </script>
    <script src="{{ asset('assets/js/rector/dashboardRector.js') }}"></script>
@endpush