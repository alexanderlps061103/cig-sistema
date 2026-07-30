@extends('layouts.app')

@section('title', 'Listado de Actividades e Inscritos')

@push('styles')
    <!-- Vinculación del CSS de la vista con los nombres de archivo actualizados -->
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/listadoInscripcion.css') }}">
@endpush

@section('content')
<div class="listado-container">

    <!-- Encabezado de la página -->
    <header class="main-header">
        <div class="header-welcome">
            <h1>Listado de Actividades</h1>
            <p>Monitoreo de actividades académicas e inscripciones de estudiantes y público general.</p>
        </div>
        <div class="header-search">
            <input type="text" id="filter-search-input" placeholder="Buscar actividad..." class="search-input">
        </div>
    </header>

    <!-- Sección de Actividades -->
    <section class="actividades-accordion-wrapper">
        @if(count($actividades) > 0)
            <div class="accordion-container">
                @foreach($actividades as $act)
                    @php
                        $inscritosCount = count($act->inscripciones);
                    @endphp
                    <div class="accordion-item" data-nombre="{{ $act->nombre }}">
                        
                        <!-- Encabezado del Acordeón -->
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <div class="activity-info">
                                <span class="tag-tipo">{{ $act->tipo->nombre ?? 'Actividad' }}</span>
                                <h3 class="activity-title">{{ $act->nombre }}</h3>
                                <div class="metadata-row">
                                    <span><i class="fa-regular fa-calendar"></i> {{ $act->fecha->translatedFormat('d \d\e F, Y') }}</span>
                                    <span><i class="fa-solid fa-users"></i> Inscritos: <strong>{{ $inscritosCount }}</strong></span>
                                    <span><i class="fa-solid fa-location-dot"></i> {{ $act->salon->nombre ?? 'Por definir' }}</span>
                                </div>
                            </div>
                            <div class="accordion-icon-box">
                                <i class="fa-solid fa-chevron-down icon-arrow"></i>
                            </div>
                        </div>

                        <!-- Contenido Colapsable (Tabla de Inscritos) -->
                        <div class="accordion-content">
                            <div class="content-inner">
                                @if($inscritosCount > 0)
                                    <div class="table-responsive">
                                        <table class="participants-table">
                                            <thead>
                                                <tr>
                                                    <th>Nro</th>
                                                    <th>Nombre Completo</th>
                                                    <th>Cédula</th>
                                                    <th>Teléfono</th>
                                                    <th>Tipo de Usuario</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($act->inscripciones as $index => $insc)
                                                    @php
                                                        // Variables defensivas por defecto para evitar excepciones si los perfiles no están completos
                                                        $nombre = 'Datos no registrados';
                                                        $cedula = 'N/A';
                                                        $telefono = 'N/A';
                                                        $tipoUsuario = 'Desconocido';

                                                        if ($insc->estudiante && $insc->estudiante->persona) {
                                                            $nombre = $insc->estudiante->persona->nombres . ' ' . $insc->estudiante->persona->apellidos;
                                                            $cedula = $insc->estudiante->persona->cedula ?? 'N/A';
                                                            $telefono = $insc->estudiante->persona->telefono ?? 'N/A';
                                                            $tipoUsuario = 'Estudiante Regular';
                                                        } elseif ($insc->publicoGeneral) {
                                                            $nombre = $insc->publicoGeneral->nombres . ' ' . $insc->publicoGeneral->apellidos;
                                                            $cedula = $insc->publicoGeneral->cedula ?? 'N/A';
                                                            $telefono = $insc->publicoGeneral->telefono ?? 'N/A';
                                                            $tipoUsuario = 'Público General';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td class="bold-text">{{ $nombre }}</td>
                                                        <td>{{ $cedula }}</td>
                                                        <td>{{ $telefono }}</td>
                                                        <td>
                                                            <span class="user-badge {{ $insc->id_estudiante ? 'badge-estudiante' : 'badge-publico' }}">
                                                                {{ $tipoUsuario }}
                                                            </span>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($insc->fecha_registro)->format('d/m/Y') }}</td>
                                                        <td>
                                                            <span class="status-badge state-{{ $insc->estado }}">
                                                                {{ ucfirst($insc->estado) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-participants">
                                        <i class="fa-solid fa-users-slash"></i>
                                        <p>No se han registrado inscripciones para esta actividad todavía.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
            
            <div id="no-results-box" class="empty-search" style="display: none;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <p>No se encontraron actividades con ese nombre.</p>
            </div>
        @else
            <div class="empty-activities">
                <i class="fa-solid fa-calendar-xmark"></i>
                <p>No se encuentran actividades registradas en el sistema.</p>
            </div>
        @endif
    </section>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/coordinador/listadoInscripcion.js') }}"></script>
@endpush