@extends('layouts.app')

@section('title', 'Solicitudes de Empleo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/solicitudes.css') }}">
@endpush

@section('content')
<div class="main-header">
    <h1><i class="fa-solid fa-file-circle-check"></i> Solicitudes de Empleo</h1>
    <p class="text-muted">Revisa y gestiona los postulantes para el cuerpo docente y administrativo.</p>
</div>

{{-- Barra de Filtros --}}
<div class="filter-bar">
    <a href="{{ route('rector.solicitudes.index') }}" class="filter-pill {{ !request('estado') ? 'active' : '' }}">Todas</a>
    <a href="{{ route('rector.solicitudes.index', ['estado' => 'pendiente']) }}" class="filter-pill {{ request('estado') == 'pendiente' ? 'active' : '' }}">Pendientes</a>
    <a href="{{ route('rector.solicitudes.index', ['estado' => 'aprobada']) }}" class="filter-pill {{ request('estado') == 'aprobada' ? 'active' : '' }}">Aprobadas</a>
    <a href="{{ route('rector.solicitudes.index', ['estado' => 'rechazada']) }}" class="filter-pill {{ request('estado') == 'rechazada' ? 'active' : '' }}">Rechazadas</a>
</div>

<div class="table-card">
    <table class="solicitud-table">
        <thead>
            <tr>
                <th>Postulante</th>
                <th>Profesión / Especialidad</th>
                <th>Fecha Envío</th>
                <th>CV</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($solicitudes as $s)
            <tr>
                <td>
                    <div style="font-weight: 600;">{{ $s->persona->nombres }} {{ $s->persona->apellidos }}</div>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">{{ $s->persona->usuario->email ?? '' }}</div>
                </td>
                <td>
                    {{-- Corregido: Se busca en Curriculum primero, pues aún no es Docente --}}
                    <strong>{{ $s->persona->curriculum->profesion->nombre ?? ($s->persona->curriculum->especialidad ?? 'Sin Profesión') }}</strong>
                </td>
                <td>{{ $s->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($s->persona->curriculum && $s->persona->curriculum->archivo_cv)
                        <a href="{{ asset('storage/'.$s->persona->curriculum->archivo_cv) }}" target="_blank" class="cv-link">
                            <i class="fa-solid fa-file-pdf"></i> Ver CV
                        </a>
                    @else
                        <span class="text-muted">No adjuntado</span>
                    @endif
                </td>
                <td>
                    <span class="badge-status status-{{ $s->estado }}">
                        {{ strtoupper($s->estado) }}
                    </span>
                </td>
                <td>
                    @if($s->estado == 'pendiente')
                        <button class="btn-action btn-rol"
                                onclick="abrirModalProcesar({{ $s->id }}, '{{ $s->persona->nombres }} {{ $s->persona->apellidos }}', '{{ $s->persona->curriculum->profesion->nombre ?? 'N/A' }}', '{{ $s->mensaje ?? 'Sin mensaje adicional.' }}')"
                                title="Procesar Solicitud">
                            <i class="fa-solid fa-user-check"></i> Evaluar
                        </button>
                    @else
                        <span style="font-size: 0.8rem; color: var(--color-text-muted);">
                            <i class="fa-solid fa-check-double"></i> Procesado
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                    No se encontraron solicitudes con estos criterios.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL PROCESAR SOLICITUD --}}
<div id="modalProcesar" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close" type="button" onclick="cerrarModal()">&times;</button>
        <h3 style="margin-bottom: 1rem;"><i class="fa-solid fa-gavel"></i> Procesar Postulación</h3>

        <div style="background: var(--color-bg-light); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border-left: 4px solid var(--color-primary);">
            <p style="margin-bottom: 0.4rem;"><strong>Postulante:</strong> <span id="nombrePostulante"></span></p>
            <p style="margin-bottom: 0.4rem;"><strong>Profesión Registrada:</strong> <span id="profesionPostulante" style="font-weight: bold; color: var(--color-primary);"></span></p>
            <p style="margin-bottom: 0;"><strong>Mensaje:</strong> <span id="mensajePostulante" style="font-style: italic; font-size: 0.9rem;"></span></p>
        </div>

        <form action="" method="POST" id="formProcesarSolicitud">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; margin-bottom: 0.5rem; font-weight:600;">Decisión de Contratación:</label>
                <select name="estado" id="estadoSolicitud" class="search-input" style="width: 100%; padding: 0.7rem; border-radius: 0.5rem; border: 1px solid var(--color-border);" required>
                    <option value="aprobada">Aprobar - Asignar Rol y Cargo de Docente</option>
                    <option value="rechazada">Rechazar - Conservar Perfil Público</option>
                </select>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 0.8rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #166534;">
                <i class="fa-solid fa-circle-info"></i> <strong>Nota:</strong> Al aprobar, el sistema cambiará automáticamente el rol a <strong>Docente</strong> y creará su expediente de empleado con su profesión correspondiente.
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="filter-pill" onclick="cerrarModal()" style="border: none; cursor: pointer;">Cancelar</button>
                <button type="submit" class="modal-btn btn-primary" style="width: 100%; border: none; cursor: pointer; padding: 1rem; background-color: var(--color-brand-blue); color: white; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.8rem; margin-top: 1rem;">
    <i class="fa-solid fa-circle-check"></i>
    Confirmar Decisión
</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/rector/solicitudes.js') }}"></script>
@endpush
