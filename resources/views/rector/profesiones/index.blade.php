@extends('layouts.app')

@section('title', 'Gestionar Profesiones')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/estructura/profesion.css') }}">
@endpush

@section('content')
<header class="main-header">
    <div class="header-welcome">
        <h1>Gestionar Profesiones</h1>
        <p>Administra las especialidades académicas de los docentes e invitados</p>
    </div>
    <div class="header-date">
        <i class="fa-regular fa-calendar"></i>
        <span>{{ now()->translatedFormat('F, Y') }}</span>
    </div>
</header>

{{-- Alertas Toast --}}
<div class="alerts-container">
    @if(session('success'))
        <div class="alert-message success-alert">
            <i class="fa-solid fa-circle-check"></i> <span>{{ session('success') }}</span>
        </div>
    @endif
</div>

<section class="crud-container">
    {{-- Barra de herramientas --}}
    <div class="table-actions-bar">
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="search-profesion" class="search-input" placeholder="Buscar profesión o descripción...">
        </div>

        <div class="filters-wrapper">
            <select class="action-select" id="filter-status">
                <option value="all">Todos los estados</option>
                <option value="active">Habilitadas</option>
                <option value="inactive">Inhabilitadas</option>
            </select>

            <button class="btn-primary-action" id="btn-open-create">
                <i class="fa-solid fa-plus"></i> Agregar Profesión
            </button>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profesión / Especialidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($registros as $p)
                <tr class="data-row"
                    data-nombre="{{ strtolower($p->nombre) }}"
                    data-descripcion="{{ strtolower($p->descripcion) }}"
                    data-estado="{{ $p->estado ? 'active' : 'inactive' }}">
                    <td><strong>{{ $p->id }}</strong></td>
                    <td>
                        <div class="table-primary-text">{{ $p->nombre }}</div>
                        <div class="table-secondary-text">{{ Str::limit($p->descripcion, 50) ?: 'Sin descripción' }}</div>
                    </td>
                    <td>
                        <span class="status-badge {{ $p->estado ? 'status-active' : 'status-inactive' }}">
                            {{ $p->estado ? 'Habilitada' : 'Inhabilitada' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action-edit" onclick="openEditModal('{{ $p->id }}', '{{ $p->nombre }}', '{{ $p->descripcion }}', '{{ $p->estado ? 'active' : 'inactive' }}')">
                                <i class="fa-solid fa-pen"></i> Editar
                            </button>
                            <button class="btn-action-delete" onclick="openToggleModal('{{ $p->id }}', '{{ $p->nombre }}', {{ $p->estado ? 'true' : 'false' }})">
                                <i class="fa-solid {{ $p->estado ? 'fa-ban' : 'fa-check-circle' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-5">No hay profesiones registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $registros->links() }}
    </div>
</section>

<!-- MODAL CREAR -->
<div class="modal-overlay" id="create-modal">
    <div class="modal-container">
        <div class="modal-header"><h3>Nueva Profesión</h3></div>
        <form action="{{ route('rector.profesiones.store') }}" method="POST" id="create-form">
            @csrf
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-user-tie input-row-icon"></i>
                    <div class="input-container">
                        <label>Nombre de la Profesión</label>
                        <input type="text" name="nombre" id="create-nombre" required>
                    </div>
                </div>
                <div class="form-group-row align-start">
                    <i class="fa-solid fa-align-left input-row-icon mt-xs"></i>
                    <div class="input-container">
                        <label>Descripción (Opcional)</label>
                        <textarea name="descripcion" id="create-descripcion" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" id="btn-cancel-create">Cancelar</button>
                <button type="submit" class="modal-btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal-overlay" id="edit-modal">
    <div class="modal-container">
        <div class="modal-header"><h3>Actualizar Profesión</h3></div>
        <form method="POST" id="edit-form">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group-row">
                    <div class="input-container">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="edit-nombre" required>
                    </div>
                </div>
                <div class="form-group-row align-start">
                    <div class="input-container">
                        <label>Descripción</label>
                        <textarea name="descripcion" id="edit-descripcion" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" id="btn-cancel-edit">Cerrar</button>
                <button type="submit" class="modal-btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TOGGLE ESTADO -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal-container modal-danger-layout">
        <form id="toggle-form" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body text-center">
                <div class="danger-icon-wrapper" id="toggle-icon-div">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h3 id="toggle-title-text">¿Desea cambiar el estado?</h3>
                <p class="delete-sub-text">Profesión: <strong id="toggle-item-name"></strong></p>
                <div class="modal-footer" style="border:none; justify-content:center; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" class="modal-btn btn-secondary" id="btn-cancel-delete">Cancelar</button>
                    <button type="submit" class="modal-btn" id="btn-confirm-toggle">Confirmar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/estructura/profesion.js') }}"></script>
@endpush