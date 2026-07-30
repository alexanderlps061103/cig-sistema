@extends('layouts.app')

@section('title', 'Gestión de Planificaciones - UNEY')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/entidades_crud/planificacion.css') }}">
@endpush

@section('content')
<header class="main-header">
    <div class="header-welcome">
        <h1>Planificaciones Académicas</h1>
        <p>Gestión y administración de los períodos anuales de la institución</p>
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

<section class="crud-container">
    <div class="table-actions-bar">
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" placeholder="Buscar planificación..." class="search-input" id="search-planificacion" autocomplete="off">
        </div>

        <div class="filters-wrapper">
            <button class="btn-primary-action" id="btn-open-create">
                <i class="fa-solid fa-plus"></i> Agregar Planificación
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Fecha de Creación</th>
                    <th width="150px">Acción</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($registros as $registro)
                <tr class="data-row" 
                    data-nombre="{{ strtolower($registro->titulo) }}" 
                    data-fecha="{{ $registro->fecha_creacion->format('Y-m-d') }}">
                    <td><strong>{{ $registro->id_planificacion }}</strong></td>
                    <td>
                        <div class="table-primary-text">{{ $registro->titulo }}</div>
                        <span class="table-secondary-text">Cod: PLN-{{ str_pad($registro->id_planificacion, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td>{{ $registro->fecha_creacion->format('d/m/Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action-edit"
                                onclick="openEditModal('{{ $registro->id_planificacion }}', '{{ addslashes($registro->titulo) }}', '{{ $registro->fecha_creacion->format('Y-m-d') }}')">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action-delete"
                                onclick="openDeleteModal('{{ $registro->id_planificacion }}', '{{ addslashes($registro->titulo) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-state-row" id="empty-row">
                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No hay planificaciones registradas aún.
                    </td>
                </tr>
                @endforelse
                <tr class="empty-state-row" id="no-results-row" style="display: none;">
                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No se encontraron resultados que coincidan con la búsqueda.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- MODAL PARA CREAR -->
<div class="modal-overlay @if($errors->any() && !old('_method')) active @endif" id="create-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Registrar Planificación</h3>
            <button class="modal-close" id="btn-close-create-modal">&times;</button>
        </div>
        <form id="create-form" action="{{ route('coordinador.planificacion.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-tag input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-titulo">Título de la Planificación</label>
                        <input type="text" id="create-titulo" name="titulo" placeholder="Ej. Planificación Académica 2026" autocomplete="off" value="{{ old('titulo') }}" class="@error('titulo') val-red @enderror">
                        @error('titulo')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-regular fa-calendar input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-fecha">Fecha de Creación</label>
                        <input type="date" id="create-fecha" name="fecha_creacion" value="{{ old('fecha_creacion') }}" class="@error('fecha_creacion') val-red @enderror">
                        @error('fecha_creacion')
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
            <h3>Modificar Planificación</h3>
            <button class="modal-close" id="btn-close-edit-modal">&times;</button>
        </div>
        <!-- Reconstrucción de action mediante old ID en recarga tras fallos de validación -->
        <form id="edit-form" action="{{ old('edit_id') ? route('coordinador.planificacion.update', ['id' => old('edit_id')]) : '' }}" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" id="edit-id" name="edit_id" value="{{ old('edit_id') }}">

            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-tag input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-titulo">Título de la Planificación</label>
                        <input type="text" id="edit-titulo" name="titulo" autocomplete="off" value="{{ old('titulo') }}" class="@error('titulo') val-red @enderror">
                        @error('titulo')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-regular fa-calendar input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-fecha">Fecha de Creación</label>
                        <input type="date" id="edit-fecha" name="fecha_creacion" value="{{ old('fecha_creacion') }}" class="@error('fecha_creacion') val-red @enderror">
                        @error('fecha_creacion')
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

<!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal-container modal-danger-layout">
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h3>Eliminar Planificación</h3>
                <button type="button" class="modal-close" id="btn-close-delete-modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div class="danger-icon-wrapper">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="delete-warning-text">
                    ¿Está seguro de que desea eliminar la planificación <strong id="delete-item-name"></strong>?
                </p>
                <p class="delete-sub-text">Esta acción eliminará el registro del sistema de forma permanente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" id="btn-cancel-delete">Cancelar</button>
                <button type="submit" class="modal-btn btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/coordinador/entidades_crud/planificacion.js') }}"></script>
@endpush