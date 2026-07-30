@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/pasantias.css') }}">
@endpush

@section('content')
<div class="main-header">
    <h1><i class="fa-solid fa-graduation-cap"></i> Inducción a Pasantías</h1>
    <p class="text-muted">Gestión y aprobación de solicitudes de inducción académica.</p>
</div>

<div class="career-highlight">
    <i class="fa-solid fa-circle-info"></i>
    <strong>Recordatorio:</strong> Solo los estudiantes de la carrera <strong>Ciencias y Culturas Alimentarias</strong> están habilitados para solicitar inducción a pasantías en este módulo.
</div>

<div class="table-card">
    <table class="pasantia-table">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Carrera</th>
                <th>Fecha Solicitud</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($solicitudes as $s)
            @php
                $esCarreraValida = ($s->estudiante->carrera->nombre == 'Ciencias y Culturas Alimentarias');
            @endphp
            <tr>
                <td>
                    <div style="font-weight: 700;">{{ $s->estudiante->persona->nombres }} {{ $s->estudiante->persona->apellidos }}</div>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">CI: {{ $s->estudiante->persona->cedula }}</div>
                </td>
                <td>
                    <span class="badge-carrera {{ $esCarreraValida ? 'carrera-valida' : '' }}">
                        <i class="fa-solid {{ $esCarreraValida ? 'fa-check-circle' : 'fa-circle-xmark' }}"></i>
                        {{ $s->estudiante->carrera->nombre }}
                    </span>
                </td>
                <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge-status status-{{ $s->estado }}">
                        {{ strtoupper($s->estado) }}
                    </span>
                </td>
                <td>
                    @if($s->estado == 'pendiente' && $esCarreraValida)
                        <button class="btn-action btn-rol"
                                onclick="abrirModalPasantia({{ $s->id }}, '{{ $s->estudiante->persona->nombres }}')"
                                title="Procesar Solicitud">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </button>
                    @elseif(!$esCarreraValida)
                        <span class="text-muted" style="font-size: 0.7rem;">Carrera no aplica</span>
                    @else
                        <span class="text-muted" style="font-size: 0.7rem;">Completado</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                    No hay solicitudes de inducción registradas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem;">
    {{ $solicitudes->links() }}
</div>

{{-- MODAL PROCESAR INDUCCIÓN --}}
<div id="modalProcesarPasantia" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close" style="float:right; border:none; background:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        <h3 style="margin-bottom: 1rem; color: var(--color-pasantia);">Procesar Inducción</h3>

        <p>¿Desea aprobar la solicitud de inducción para el estudiante <strong><span id="nombreEstudiante"></span></strong>?</p>

        <form action="" method="POST" id="formPasantia">
            @csrf
            <div style="margin: 1.5rem 0;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Decisión final:</label>
                <select name="estado" class="search-input" style="width:100%; padding:0.8rem; border-radius:0.5rem; border:1px solid var(--color-border);" required>
                    <option value="aprobada">Aprobar Inducción</option>
                    <option value="rechazada">Rechazar Solicitud</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Observaciones (Opcional):</label>
                <textarea name="observacion" class="search-input" style="width:100%; height:80px; padding:0.8rem; border-radius:0.5rem; border:1px solid var(--color-border); resize:none;"></textarea>
            </div>

            <button type="submit" class="filter-pill active" style="width:100%; padding:1rem; border:none; cursor:pointer;">
                Guardar y Finalizar
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/rector/pasantias.js') }}"></script>
@endpush
