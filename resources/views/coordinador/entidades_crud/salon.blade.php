@extends('layouts.app')

@section('title', 'Gestión de Salones - UNEY')

@push('styles')
    <!-- CSS Propio de esta vista -->
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/entidades_crud/salon.css') }}">
@endpush

@section('content')
    <!-- CONTENIDO PRINCIPAL -->
    <!-- Cabecera de Contenido -->
    <header class="main-header">
        <div class="header-welcome">
            <h1>Gestión de Salones</h1>
            <p>Configuración y asignación de salones, laboratorios y espacios físicos de la institución</p>
        </div>
        <div class="header-date">
            <i class="fa-regular fa-calendar"></i>
            <span id="current-date-display">{{ now()->translatedFormat('F, Y') }}</span>
        </div>
    </header>

    <!-- Contenedor de Alertas de Laravel (Flotante) -->
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

    <!-- Contenedor Principal de la Tabla -->
    <section class="crud-container">
        
        <!-- Barra Superior de Acciones Filtros (Buscar, Filtrar y Agregar) -->
        <div class="table-actions-bar">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" placeholder="Buscar salón..." class="search-input" id="search-salon" autocomplete="off">
            </div>

            <div class="filters-wrapper">
                <select class="action-select" id="filter-status">
                    <option value="all">Todos los estados</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>

                <button class="btn-primary-action" id="btn-open-create">
                    <i class="fa-solid fa-plus"></i> Agregar Salón
                </button>
            </div>
        </div>

        <!-- Tabla de Datos -->
        <div class="table-responsive">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Salón / Aula</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th width="150px">Acción</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse($registros as $espacio)
                    <tr class="data-row" 
                        data-nombre="{{ strtolower($espacio->nombre) }}" 
                        data-capacidad="{{ $espacio->capacidad }}" 
                        data-estado="{{ $espacio->estado ? 'active' : 'inactive' }}">
                        <td><strong>{{ $espacio->id_salon }}</strong></td>
                        <td>
                            <div class="table-primary-text">{{ $espacio->nombre }}</div>
                            <span class="table-secondary-text">Cod: SAL-0{{ $espacio->id_salon }}</span>
                        </td>
                        <td><strong>{{ $espacio->capacidad }} Estudiantes</strong></td>
                        <td>
                            @if($espacio->estado)
                                <span class="status-badge status-active">Activo</span>
                            @else
                                <span class="status-badge status-inactive" style="background-color: #f3f4f6; color: #4b5563;">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action-edit" 
                                    onclick="openEditModal('{{ $espacio->id_salon }}', '{{ addslashes($espacio->nombre) }}', '{{ $espacio->capacidad }}', '{{ $espacio->estado ? 'active' : 'inactive' }}')">
                                        <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="btn-action-delete" 
                                    onclick="openDeleteModal('{{ $espacio->id_salon }}', '{{ addslashes($espacio->nombre) }}')">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-state-row" id="empty-row">
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                            No hay salones registrados.
                        </td>
                    </tr>
                    @endforelse
                    <!-- Fila defensiva para búsquedas vacías en JS -->
                    <tr class="empty-state-row" id="no-results-row" style="display: none;">
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                            No se encontraron salones que coincidan con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación inferior -->
        @if(method_exists($registros, 'hasPages') && $registros->hasPages())
            <div class="table-pagination" style="margin-top: 15px;">
                {{ $registros->links() }}
            </div>
        @endif
    </section>

    <!-- MODAL PARA CREAR -->
    <div class="modal-overlay @if($errors->any() && !old('_method')) active @endif" id="create-modal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Registrar Salón</h3>
                <button class="modal-close" id="btn-close-create-modal">&times;</button>
            </div>
            <form id="create-form" action="{{ route('coordinador.entidades_crud.store', ['modulo' => $modulo]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group-row">
                        <i class="fa-solid fa-door-open input-row-icon"></i>
                        <div class="input-container">
                            <label for="create-nombre">Nombre del Salón / Aula</label>
                            <input type="text" id="create-nombre" name="nombre" placeholder="Ej. Laboratorio de Informática" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror" autocomplete="off">
                            @error('nombre')
                                <span class="error-input-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-row">
                        <i class="fa-solid fa-users input-row-icon"></i>
                        <div class="input-container">
                            <label for="create-capacidad">Capacidad de Estudiantes</label>
                            <input type="number" id="create-capacidad" name="capacidad" min="1" placeholder="Ej. 30" value="{{ old('capacidad') }}" class="@error('capacidad') val-red @enderror">
                            @error('capacidad')
                                <span class="error-input-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-row">
                        <i class="fa-solid fa-toggle-on input-row-icon"></i>
                        <div class="input-container">
                            <label for="create-estado">Estado del Salón</label>
                            <select id="create-estado" name="estado">
                                <option value="active" {{ old('estado') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ old('estado') == 'inactive' ? 'selected' : '' }}>Deshabilitado</option>
                            </select>
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
                <h3>Modificar Salón</h3>
                <button class="modal-close" id="btn-close-edit-modal">&times;</button>
            </div>
            <!-- El action se resolverá mediante Javascript en todos los escenarios (incluyendo recargas por fallos de validación) -->
            <form id="edit-form" action="{{ old('edit_id') ? route('coordinador.entidades_crud.update', ['modulo' => $modulo, 'id' => old('edit_id')]) : '' }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Input oculto para recordar el ID del registro que se está editando -->
                <input type="hidden" id="edit-id" name="edit_id" value="{{ old('edit_id') }}">

                <div class="modal-body">
                    <div class="form-group-row">
                        <i class="fa-solid fa-door-open input-row-icon"></i>
                        <div class="input-container">
                            <label for="edit-nombre">Nombre del Salón / Aula</label>
                            <input type="text" id="edit-nombre" name="nombre" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror" autocomplete="off">
                            @error('nombre')
                                <span class="error-input-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-row">
                        <i class="fa-solid fa-users input-row-icon"></i>
                        <div class="input-container">
                            <label for="edit-capacidad">Capacidad de Estudiantes</label>
                            <input type="number" id="edit-capacidad" name="capacidad" min="1" value="{{ old('capacidad') }}" class="@error('capacidad') val-red @enderror">
                            @error('capacidad')
                                <span class="error-input-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-row">
                        <i class="fa-solid fa-toggle-on input-row-icon"></i>
                        <div class="input-container">
                            <label for="edit-estado">Estado del Salón</label>
                            <select id="edit-estado" name="estado">
                                <option value="active" {{ old('estado') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ old('estado') == 'inactive' ? 'selected' : '' }}>Deshabilitado</option>
                            </select>
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

    <!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
    <div class="modal-overlay" id="delete-modal">
        <div class="modal-container modal-danger-layout">
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h3>Inhabilitar Salón</h3>
                    <button type="button" class="modal-close" id="btn-close-delete-modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <div class="danger-icon-wrapper">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <p class="delete-warning-text">
                        ¿Está seguro de que desea inhabilitar el salón <strong id="delete-item-name"></strong>?
                    </p>
                    <p class="delete-sub-text">Esta acción cambiará el estado del salón a deshabilitado.</p>
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
    <!-- Script propio de esta vista -->
    <script src="{{ asset('assets/js/coordinador/entidades_crud/salon.js') }}"></script>
@endpush