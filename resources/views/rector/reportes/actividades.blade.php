@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/reportes.css') }}">
@endpush

@section('content')
<div class="main-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1><i class="fa-solid fa-calendar-check"></i> Verificar Actividades</h1>
            <p class="text-muted">Monitoreo de gestión de coordinadores y validación académica.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('rector.exportar.actividades.pdf') }}" class="filter-pill active">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>
</div>

<div class="report-grid">
    {{-- Actividades por Tipo --}}
    <div class="chart-card">
        <div class="chart-title"><i class="fa-solid fa-chart-pie"></i> Frecuencia por Tipo</div>
        @foreach($frecuenciaActividades as $f)
            <div class="stat-bar-container">
                <div class="stat-bar-info">
                    <span>{{ $f->nombre }}</span>
                    <strong>{{ $f->total }}</strong>
                </div>
                <div class="stat-bar-bg">
                    @php $porcentaje = ($f->total / ($frecuenciaActividades->sum('total') ?: 1)) * 100; @endphp
                    <div class="stat-bar-fill" style="width: {{ $porcentaje }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gestión de Coordinadores --}}
    <div class="chart-card">
        <div class="chart-title"><i class="fa-solid fa-user-gear"></i> Gestión de Coordinadores</div>
        @foreach($reporteCoordinadores as $c)
            <div class="stat-bar-container">
                <div class="stat-bar-info">
                    <span>{{ $c->nombres }} {{ $c->apellidos }}</span>
                    <strong>{{ $c->actividades_organizadas_count }} act.</strong>
                </div>
                <div class="stat-bar-bg">
                    <div class="stat-bar-fill" style="width: 70%; background: #6366f1;"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Tabla de Verificación --}}
<div class="table-card">
    <div style="padding: 1rem; border-bottom: 1px solid var(--color-border); font-weight: 700;">
        Listado de Actividades Recientes
    </div>
    <table class="user-table">
        <thead>
            <tr>
                <th>Actividad</th>
                <th>Coordinador</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Verificación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actividades as $act)
            <tr>
                <td>
                    <div class="user-name">{{ $act->nombre }}</div>
                    <div style="font-size: 0.7rem;" class="badge-role">{{ $act->tipo->nombre ?? 'N/A' }}</div>
                </td>
                <td>{{ $act->creador->nombres ?? 'N/A' }} {{ $act->creador->apellidos ?? '' }}</td>
                <td>{{ $act->fecha ? $act->fecha->format('d/m/Y') : 'N/A' }}</td>
                <td>
                    <span class="badge {{ $act->estado == 'activa' ? 'badge-active' : 'badge-inactive' }}">
                        {{ strtoupper($act->estado) }}
                    </span>
                </td>
                <td>
                    @if($act->verificado_at)
                        <span class="badge-verificado"><i class="fa-solid fa-check-double"></i> Verificada</span>
                    @else
                        <span class="badge-pendiente"><i class="fa-solid fa-clock"></i> Pendiente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/rector/reportes.js') }}"></script>
@endpush