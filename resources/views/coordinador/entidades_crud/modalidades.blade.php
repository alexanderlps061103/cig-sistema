@extends('layouts.app')

@section('title', 'Gestión de Modalidades - UNEY')

@push('styles')
    <!-- CSS Propio de esta vista -->
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/entidades_crud/modalidad.css') }}">
@endpush

@section('content')
<!-- Cabecera de Contenido -->
<header class="main-header">
    <div class="header-welcome">
        <h1>Modalidades de Estudio</h1>
        <p>Gestión y configuración de las modalidades disponibles (presencial, semipresencial, virtual, etc.)</p>
    </div>
    <div class="header-date">
        <i class="fa-regular fa-calendar"></i>
        <span id="current-date-display">{{ now()->translatedFormat('F, Y') }}</span>
    </div>
</header>

<!-- Contenedor de Alertas Flotantes (Evita alterar la estructura HTML principal) -->
<div class="alerts-container">
    @if(session('success'))
        <div class="alert-message success-alert">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-message error-alert">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-message error-alert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <ul style="margin: 0; padding-left: 1.2rem; list-style-type: none;">
                @foreach ($errors->all() as $error)
                    <li><i class="fa-solid fa-angle-right" style="font-size: 0.8rem; margin-right: 0.3rem;"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<!-- Contenedor Principal del CRUD -->
<section class="crud-container">

    <!-- Barra Superior de Acciones y Filtros -->
    <div class="table-actions-bar">
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" placeholder="Buscar modalidad..." class="search-input" id="search-modalidad" autocomplete="off">
        </div>

        <div class="filters-wrapper">
            <select class="action-select" id="filter-status">
                <option value="all">Todos los estados</option>
                <option value="active">Habilitadas</option>
                <option value="inactive">Inhabilitadas</option>
            </select>

            <button class="btn-primary-action" id="btn-open-create">
                <i class="fa-solid fa-plus"></i> Agregar Modalidad
            </button>
        </div>
    </div>

    <!-- Tabla de Datos -->
    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Modalidad</th>
                    <th>Estado</th>
                    <th width="150px">Acción</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($registros as $registro)
                <tr class="data-row" 
                    data-nombre="{{ strtolower($registro->nombre_modalidad) }}" 
                    data-estado="{{ $registro->estado ? 'active' : 'inactive' }}">
                    <td><strong>{{ $registro->id_modalidad }}</strong></td>
                    <td>
                        <div class="table-primary-text">{{ $registro->nombre_modalidad }}</div>
                        <span class="table-secondary-text">Cod: MOD-{{ str_pad($registro->id_modalidad, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td>
                        @if($registro->estado)
                            <span class="status-badge status-active">Habilitada</span>
                        @else
                            <span class="status-badge status-inactive">Inhabilitada</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action-edit"
                                onclick="openEditModal('{{ $registro->id_modalidad }}', '{{ addslashes($registro->nombre_modalidad) }}', '{{ $registro->estado ? 'active' : 'inactive' }}')"
                                title="Editar">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action-delete"
                                onclick="openDeleteModal('{{ $registro->id_modalidad }}', '{{ addslashes($registro->nombre_modalidad) }}')"
                                title="Inhabilitar">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-state-row" id="empty-row">
                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No hay modalidades registradas aún.
                    </td>
                </tr>
                @endforelse
                <!-- Fila defensiva para búsquedas sin resultados -->
                <tr class="empty-state-row" id="no-results-row" style="display: none;">
                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No se encontraron resultados que coincidan con la búsqueda.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(method_exists($registros, 'hasPages') && $registros->hasPages())
        <div class="pagination-container" style="margin-top: 15px;">
            {{ $registros->links() }}
        </div>
    @endif
</section>

<!-- MODAL PARA CREAR -->
<div class="modal-overlay @if($errors->any() && !old('_method')) active @endif" id="create-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Registrar Modalidad</h3>
        </div>
        <form id="create-form" action="{{ route('coordinador.entidades_crud.store', ['modulo' => $modulo]) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-tag input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-nombre">Nombre de la Modalidad</label>
                        <input type="text" id="create-nombre" name="nombre_modalidad" placeholder="Ej. Presencial" autocomplete="off" value="{{ old('nombre_modalidad') }}" class="@error('nombre_modalidad') val-red @enderror">
                        @error('nombre_modalidad')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-solid fa-toggle-on input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-estado">Estado</label>
                        <select id="create-estado" name="estado">
                            <option value="active" {{ old('estado') == 'active' ? 'selected' : '' }}>Habilitada</option>
                            <option value="inactive" {{ old('estado') == 'inactive' ? 'selected' : '' }}>Inhabilitada</option>
                        </select>
                        @error('estado')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
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

<!-- MODAL PARA MODIFICAR -->
<div class="modal-overlay @if($errors->any() && old('_method') === 'PUT') active @endif" id="edit-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Modificar Modalidad</h3>
        </div>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-tag input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-nombre">Nombre de la Modalidad</label>
                        <input type="text" id="edit-nombre" name="nombre_modalidad" autocomplete="off" value="{{ old('nombre_modalidad') }}" class="@error('nombre_modalidad') val-red @enderror">
                        @error('nombre_modalidad')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-solid fa-toggle-on input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-estado">Estado</label>
                        <select id="edit-estado" name="estado">
                            <option value="active" {{ old('estado') == 'active' ? 'selected' : '' }}>Habilitada</option>
                            <option value="inactive" {{ old('estado') == 'inactive' ? 'selected' : '' }}>Inhabilitada</option>
                        </select>
                        @error('estado')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" id="btn-cancel-edit">Cancelar</button>
                <button type="submit" class="modal-btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DE INHABILITACIÓN -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal-container modal-danger-layout">
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h3>Inhabilitar Modalidad</h3>
            </div>
            <div class="modal-body text-center">
                <div class="danger-icon-wrapper">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="delete-warning-text">
                    ¿Está seguro de que desea inhabilitar la modalidad <strong id="delete-item-name"></strong>?
                </p>
                <p class="delete-sub-text">Esta acción cambiará el estado de la modalidad a inhabilitada en el sistema.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" id="btn-cancel-delete">Cancelar</button>
                <button type="submit" class="modal-btn btn-danger">Inhabilitar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Script Propio de esta vista -->
    <script src="{{ asset('assets/js/coordinador/entidades_crud/modalidad.js') }}"></script>
@endpush