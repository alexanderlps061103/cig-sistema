@extends('layouts.app')

@section('title', 'Gestión de Tipos de Estudiante')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/estructura/tipo_de_estudiante.css') }}">
@endpush

@section('content')
    <header class="main-header">
        <div class="header-welcome">
            <h1>Tipos de Estudiante</h1>
            <p>Configuración y clasificación de perfiles estudiantiles</p>
        </div>
        <div class="header-date">
            <i class="fa-regular fa-calendar"></i>
            <span>{{ now()->translatedFormat('F, Y') }}</span>
        </div>
    </header>

    <div class="alerts-container">
        @if(session('success'))
            <div class="alert-message success-alert">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
    </div>

    <section class="crud-container">
        <div class="table-actions-bar">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" placeholder="Buscar por nombre..." class="search-input" id="search-tipo-estudiante">
            </div>

            <div class="filters-wrapper">
                <select class="action-select" id="filter-status">
                    <option value="all">Todos los estados</option>
                    <option value="active">Habilitados</option>
                    <option value="inactive">Inhabilitados</option>
                </select>

                <button class="btn-primary-action" id="btn-open-create">
                    <i class="fa-solid fa-plus"></i> Agregar Tipo
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th width="100px">ID</th>
                        <th>Tipo de Estudiante</th>
                        <th>Estado</th>
                        <th width="180px">Acción</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse($registros as $tipo)
                        <tr class="data-row"
                            data-nombre="{{ strtolower($tipo->nombre) }}"
                            data-estado="{{ $tipo->estado ? 'active' : 'inactive' }}">
                            <td><strong>{{ $tipo->id }}</strong></td>
                            <td>
                                <div class="table-primary-text">{{ $tipo->nombre }}</div>
                                <span class="table-secondary-text">Código: T-EST-{{ $tipo->id }}</span>
                            </td>
                            <td>
                                <span class="status-badge {{ $tipo->estado ? 'status-active' : 'status-inactive' }}">
                                    {{ $tipo->estado ? 'Habilitado' : 'Inhabilitado' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action-edit" onclick="openEditModal('{{ $tipo->id }}', '{{ $tipo->nombre }}', '{{ $tipo->estado ? 'active' : 'inactive' }}')">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </button>
                                    <button class="btn-action-delete" onclick="openDeleteModal('{{ $tipo->id }}', '{{ $tipo->nombre }}')">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-5">No hay registros disponibles.</td></tr>
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
            <div class="modal-header"><h3>Registrar Nuevo Tipo</h3></div>
            <form action="{{ route('rector.tipo_estudiantes.store') }}" method="POST" id="create-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group-row">
                        <i class="fa-solid fa-tag input-row-icon"></i>
                        <div class="input-container">
                            <label>Nombre del Tipo</label>
                            <input type="text" id="create-nombre" name="nombre" required placeholder="Ej. Regular">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn btn-secondary" id="btn-cancel-create">Cancelar</button>
                    <button type="submit" class="modal-btn btn-primary">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDITAR -->
    <div class="modal-overlay" id="edit-modal">
        <div class="modal-container">
            <div class="modal-header"><h3>Modificar Registro</h3></div>
            <form id="edit-form" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group-row">
                        <div class="input-container">
                            <label>Nombre del Tipo</label>
                            <input type="text" id="edit-nombre" name="nombre" required>
                        </div>
                    </div>
                    <div class="form-group-row">
                        <div class="input-container">
                            <label>Estado</label>
                            <select id="edit-estado" name="estado">
                                <option value="active">Habilitado</option>
                                <option value="inactive">Inhabilitado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn btn-secondary" id="btn-cancel-edit">Cerrar</button>
                    <button type="submit" class="modal-btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DESACTIVAR -->
    <div class="modal-overlay" id="delete-modal">
        <div class="modal-container modal-danger-layout">
            <form id="delete-form" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body text-center">
                    <div class="danger-icon-wrapper"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <p class="delete-warning-text">¿Cambiar estado de <strong id="delete-item-name"></strong>?</p>
                    <p class="delete-sub-text">Esta acción alternará la visibilidad del registro en el sistema.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn btn-secondary" id="btn-cancel-delete">Cancelar</button>
                    <button type="submit" class="modal-btn btn-danger">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/estructura/tipo_de_estudiante.js') }}"></script>
@endpush
