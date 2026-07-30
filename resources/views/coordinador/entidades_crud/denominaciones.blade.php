@extends('layouts.app')

@section('title', 'Gestión de Denominaciones - UNEY')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/entidades_crud/denominacion_de_la_actividad.css') }}">
@endpush

@section('content')
<header class="main-header">
    <div class="header-welcome">
        <h1>Denominaciones de Actividad</h1>
        <p>Gestión y configuración de los tipos de actividades (clases, talleres, foros, etc.)</p>
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

<section class="crud-container">
    <div class="table-actions-bar">
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" placeholder="Buscar denominación..." class="search-input" id="search-denominacion" autocomplete="off">
        </div>

        <div class="filters-wrapper">
            <select class="action-select" id="filter-status">
                <option value="all">Todos los estados</option>
                <option value="active">Habilitados</option>
                <option value="inactive">Inhabilitados</option>
            </select>

            <button class="btn-primary-action" id="btn-open-create">
                <i class="fa-solid fa-plus"></i> Agregar Denominación
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Denominación</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th width="150px">Acción</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($registros as $registro)
                <tr class="data-row" 
                    data-nombre="{{ strtolower($registro->nombre) }}" 
                    data-descripcion="{{ strtolower($registro->descripcion ?? '') }}" 
                    data-estado="{{ $registro->estado ? 'active' : 'inactive' }}">
                    <td><strong>{{ $registro->id_tipo_actividad }}</strong></td>
                    <td>
                        <div class="table-primary-text">{{ $registro->nombre }}</div>
                        <span class="table-secondary-text">Cod: ACT-{{ str_pad($registro->id_tipo_actividad, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td>{{ $registro->descripcion ?? 'Sin descripción disponible.' }}</td>
                    <td>
                        @if($registro->estado)
                            <span class="status-badge status-active">Habilitado</span>
                        @else
                            <span class="status-badge status-inactive">Inhabilitado</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action-edit"
                                onclick="openEditModal('{{ $registro->id_tipo_actividad }}', '{{ addslashes($registro->nombre) }}', '{{ addslashes($registro->descripcion) }}', '{{ $registro->duracion }}', '{{ $registro->estado ? 'active' : 'inactive' }}')">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action-delete"
                                onclick="openDeleteModal('{{ $registro->id_tipo_actividad }}', '{{ addslashes($registro->nombre) }}')">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-state-row" id="empty-row">
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No hay denominaciones registradas aún.
                    </td>
                </tr>
                @endforelse
                <tr class="empty-state-row" id="no-results-row" style="display: none;">
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
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
            <h3>Registrar Denominación</h3>
        </div>
        <form id="create-form" action="{{ route('coordinador.entidades_crud.store', ['modulo' => $modulo]) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-tag input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-nombre">Nombre de la Denominación</label>
                        <input type="text" id="create-nombre" name="nombre" placeholder="Ej. Taller" autocomplete="off" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror">
                        @error('nombre')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row align-start">
                    <i class="fa-solid fa-align-left input-row-icon mt-xs"></i>
                    <div class="input-container">
                        <label for="create-descripcion">Descripción</label>
                        <textarea id="create-descripcion" name="descripcion" placeholder="Descripción de la actividad..." rows="3" class="@error('descripcion') val-red @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-regular fa-clock input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-duracion">Duración Estimada</label>
                        <input type="time" id="create-duracion" name="duracion" step="1" value="{{ old('duracion') }}" class="@error('duracion') val-red @enderror">
                        @error('duracion')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-solid fa-toggle-on input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-estado">Estado</label>
                        <select id="create-estado" name="estado">
                            <option value="active" {{ old('estado') == 'active' ? 'selected' : '' }}>Habilitado</option>
                            <option value="inactive" {{ old('estado') == 'inactive' ? 'selected' : '' }}>Inhabilitado</option>
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
            <h3>Modificar Denominación</h3>
        </div>
        <!-- El action se resolverá mediante Javascript en todos los escenarios -->
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Campo oculto para recordar el ID del registro que se está editando -->
            <input type="hidden" id="edit-id" name="edit_id" value="{{ old('edit_id') }}">

            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-tag input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-nombre">Nombre de la Denominación</label>
                        <input type="text" id="edit-nombre" name="nombre" autocomplete="off" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror">
                        @error('nombre')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row align-start">
                    <i class="fa-solid fa-align-left input-row-icon mt-xs"></i>
                    <div class="input-container">
                        <label for="edit-descripcion">Descripción</label>
                        <textarea id="edit-descripcion" name="descripcion" rows="3" class="@error('descripcion') val-red @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-regular fa-clock input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-duracion">Duración Estimada</label>
                        <input type="time" id="edit-duracion" name="duracion" step="1" value="{{ old('duracion') }}" class="@error('duracion') val-red @enderror">
                        @error('duracion')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-solid fa-toggle-on input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-estado">Estado</label>
                        <select id="edit-estado" name="estado">
                            <option value="active" {{ old('estado') == 'active' ? 'selected' : '' }}>Habilitado</option>
                            <option value="inactive" {{ old('estado') == 'inactive' ? 'selected' : '' }}>Inhabilitado</option>
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

<!-- MODAL DE INHABILITACIÓN -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal-container modal-danger-layout">
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h3>Inhabilitar Denominación</h3>
            </div>
            <div class="modal-body text-center">
                <div class="danger-icon-wrapper">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="delete-warning-text">
                    ¿Está seguro de que desea inhabilitar la denominación <strong id="delete-item-name"></strong>?
                </p>
                <p class="delete-sub-text">Esta acción cambiará el estado de la denominación a inhabilitada.</p>
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
<script src="{{ asset('assets/js/coordinador/entidades_crud/denominacion_de_la_actividad.js') }}"></script>
@endpush