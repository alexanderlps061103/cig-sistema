@extends('layouts.app')

@section('title', 'Gestionar docentes')

@push('styles')
    {{-- Cargamos el CSS que proporcionaste --}}
    <link rel="stylesheet" href="{{ asset('assets/css/rector/docentes.css') }}">
    <style>
        /* Ajustes específicos para los elementos visuales de Docentes */
        .docente-avatar {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background: var(--color-active-bg);
            color: var(--color-brand-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            border: 2px solid var(--color-border);
            overflow: hidden;
            flex-shrink: 0;
        }
        .docente-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .docente-info-wrapper { display: flex; align-items: center; gap: 1rem; }

        .frequency-bar-container {
            width: 100%;
            max-width: 150px;
            height: 0.6rem;
            background: var(--color-hover-bg);
            border-radius: 1rem;
            overflow: hidden;
            margin-top: 0.4rem;
        }
        .frequency-fill {
            height: 100%;
            background: var(--color-brand-blue);
            border-radius: 1rem;
            transition: width 0.5s ease;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, min-minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .metric-card {
            background: var(--color-bg-sidebar);
            padding: 1.5rem;
            border-radius: 1rem;
            border: 0.08rem solid var(--color-border);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .metric-value { font-size: 2rem; font-weight: 800; color: var(--color-text-main); }
        .metric-label { font-size: 0.9rem; color: var(--color-text-muted); font-weight: 600; text-transform: uppercase; }
    </style>
@endpush

@section('content')
<div class="main-content">
    <header class="main-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1><i class="fa-solid fa-chalkboard-user" style="color: var(--color-brand-blue);"></i> Gestionar docentes</h1>
                <p style="color: var(--color-text-muted); margin-top: 0.5rem;">Personal académico clasificado por especialidad y frecuencia de ponencias.</p>
            </div>
            <a href="{{ route('rector.exportar.docentes.excel') }}" class="btn-primary-action" style="text-decoration: none;">
                <i class="fa-solid fa-file-excel"></i> Exportar a Excel
            </a>
        </div>
    </header>

    {{-- Tarjetas de Métricas Rápidas --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <span class="metric-label">Docentes Registrados</span>
            <span class="metric-value">{{ $docentesMasUsados->total() }}</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Ponencias Dictadas</span>
            <span class="metric-value">{{ $docentesMasUsados->sum('ponencias_count') }}</span>
        </div>
    </div>

    <section class="crud-container">
        {{-- Barra de búsqueda utilizando tus estilos --}}
        <div class="table-actions-bar">
            <form action="{{ route('rector.docentes.index') }}" method="GET" class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="buscar" class="search-input" placeholder="Buscar por nombre o cédula..." value="{{ request('buscar') }}">
            </form>

            <div class="filters-wrapper">
                <button type="button" class="btn-action-edit" onclick="location.reload()">
                    <i class="fa-solid fa-arrows-rotate"></i> Actualizar
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Cédula</th>
                        <th>Especialidad</th>
                        <th>Frecuencia de Sesiones</th>
                        <th width="100px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($docentesMasUsados as $d)
                    <tr>
                        <td>
                            <div class="docente-info-wrapper">
                                <div class="docente-avatar">
                                    @if($d->persona->foto)
                                        <img src="{{ asset('storage/'.$d->persona->foto) }}" alt="Foto">
                                    @else
                                        {{ substr($d->persona->nombres, 0, 1) }}{{ substr($d->persona->apellidos, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="table-primary-text">{{ $d->persona->nombres }} {{ $d->persona->apellidos }}</div>
                                    <div class="table-secondary-text">{{ $d->persona->usuario->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><strong>{{ $d->persona->cedula }}</strong></td>
                        <td>
                            <span class="status-badge" style="background-color: var(--color-hover-bg); color: var(--color-brand-blue); border: 0.08rem solid var(--color-border);">
                                {{ $d->profesion->nombre ?? 'General' }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column;">
                                <span class="table-primary-text">{{ $d->ponencias_count }} sesiones</span>
                                <div class="frequency-bar-container">
                                    @php $porcentaje = min(($d->ponencias_count / 20) * 100, 100); @endphp
                                    <div class="frequency-fill" style="width: {{ $porcentaje }}%;"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action-edit"
                                    onclick="verDetalleDocente('{{ $d->persona->nombres }} {{ $d->persona->apellidos }}', '{{ $d->profesion->nombre ?? 'General' }}', '{{ $d->ponencias_count }}', '{{ $d->persona->curriculum->experiencia ?? 'Sin descripción de experiencia registrada.' }}')"
                                    title="Ver Perfil Académico">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 4rem; color: var(--color-text-muted);">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 1rem; opacity: 0.5;"></i>
                            No se encontraron docentes registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-pagination">
            {{ $docentesMasUsados->links() }}
        </div>
    </section>
</div>

{{-- MODAL DETALLE DOCENTE (Adaptado a tus estilos) --}}
<div id="modalDocenteDetalle" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-graduate"></i> Perfil Académico</h3>
            <button class="btn-action-delete close-modal" style="border:none; background:none;">&times;</button>
        </div>

        <div class="modal-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="input-container">
                    <label>Nombre Completo</label>
                    <p id="detNombre" class="table-primary-text" style="font-size: 1.3rem;"></p>
                </div>
                <div class="input-container">
                    <label>Especialidad</label>
                    <div><span id="detProfesion" class="status-badge status-active"></span></div>
                </div>
                <div class="input-container">
                    <label>Sesiones Dictadas</label>
                    <p id="detSesiones" class="metric-value" style="color: var(--color-brand-blue);"></p>
                </div>
                <div class="input-container" style="grid-column: span 2;">
                    <label>Resumen de Experiencia</label>
                    <div style="background: var(--color-hover-bg); padding: 1.5rem; border-radius: 0.8rem; border-left: 0.4rem solid var(--color-brand-blue);">
                        <p id="detExperiencia" style="line-height: 1.6; color: var(--color-text-main); font-style: italic;"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="modal-btn btn-secondary close-modal">Cerrar Perfil</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/rector/docentes.js') }}"></script>
@endpush