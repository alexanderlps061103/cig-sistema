@extends('layouts.app')

@section('title', 'Gestión de Trimestres - UNEY')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/entidades_crud/trimestre.css') }}">
@endpush

@section('content')
<header class="main-header">
    <div class="header-welcome">
        <h1>Gestión de Trimestres</h1>
        <p>Configuración de los períodos académicos y lapsos de planificación de la institución</p>
    </div>
    <div class="header-date">
        <i class="fa-regular fa-calendar"></i>
        <span id="current-date-display">{{ now()->translatedFormat('F, Y') }}</span>
    </div>
</header>

<!-- Contenedor de Alertas Flotantes -->
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
            <input type="text" placeholder="Buscar trimestre por nombre..." class="search-input" id="search-trimestre" autocomplete="off">
        </div>

        <div class="filters-wrapper">
            <button class="btn-primary-action" id="btn-open-create">
                <i class="fa-solid fa-plus"></i> Agregar Trimestre
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Período</th>
                    <th>Fecha Inicio / Fin</th>
                    <th>Planificación Asociada</th>
                    <th width="150px">Acción</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($registros as $registro)
                <tr class="data-row" 
                    data-nombre="{{ strtolower($registro->nombre) }}" 
                    data-planificacion="{{ $registro->id_planificacion }}">
                    <td><strong>{{ $registro->id_trimestre }}</strong></td>
                    <td>
                        <div class="table-primary-text">{{ $registro->nombre }}</div>
                        <span class="table-secondary-text">Cod: TRI-{{ str_pad($registro->id_trimestre, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td>
                        <div class="table-primary-text">{{ $registro->fecha_inicio->format('d/m/Y') }}</div>
                        <span class="table-secondary-text">Hasta: {{ $registro->fecha_fin->format('d/m/Y') }}</span>
                    </td>
                    <!-- ACTUALIZADO: Muestra el título y el año de la planificación para mayor claridad -->
                    <td>
                        @if($registro->planificacion)
                            <div class="table-primary-text">{{ $registro->planificacion->titulo }}</div>
                            <span class="table-secondary-text">Año: {{ $registro->planificacion->anio }}</span>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action-edit"
                                title="Editar"
                                onclick="openEditModal('{{ $registro->id_trimestre }}', '{{ addslashes($registro->nombre) }}', '{{ $registro->fecha_inicio->format('Y-m-d') }}', '{{ $registro->fecha_fin->format('Y-m-d') }}', '{{ $registro->id_planificacion }}')">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action-delete"
                                title="Eliminar"
                                onclick="openDeleteModal('{{ $registro->id_trimestre }}', '{{ addslashes($registro->nombre) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-state-row" id="empty-row">
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No hay trimestres registrados aún.
                    </td>
                </tr>
                @endforelse
                <tr class="empty-state-row" id="no-results-row" style="display: none;">
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No se encontraron trimestres que coincidan con la búsqueda.
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
            <h3>Registrar Trimestre</h3>
            <button class="modal-close" id="btn-close-create-modal">&times;</button>
        </div>
        <form id="create-form" action="{{ route('coordinador.planificacion.trimestres.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-tag input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-nombre">Nombre del Período / Trimestre</label>
                        <input type="text" id="create-nombre" name="nombre" placeholder="Ej. Trimestre 2026-I" autocomplete="off" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror">
                        @error('nombre')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-regular fa-calendar input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-fecha-inicio">Fecha de Inicio</label>
                        <input type="date" id="create-fecha-inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" class="@error('fecha_inicio') val-red @enderror">
                        @error('fecha_inicio')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-regular fa-calendar-check input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-fecha-fin">Fecha de Culminación</label>
                        <input type="date" id="create-fecha-fin" name="fecha_fin" value="{{ old('fecha_fin') }}" class="@error('fecha_fin') val-red @enderror">
                        @error('fecha_fin')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-solid fa-sitemap input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-planificacion">Planificación Asociada</label>
                        <!-- ACTUALIZADO: Muestra "Título de la Planificación (Año)" -->
                        <select id="create-planificacion" name="id_planificacion" class="@error('id_planificacion') val-red @enderror">
                            <option value="">Seleccione una planificación...</option>
                            @foreach($planificaciones as $plan)
                                <option value="{{ $plan->id_planificacion }}" {{ old('id_planificacion') == $plan->id_planificacion ? 'selected' : '' }}>
                                    {{ $plan->titulo }} ({{ $plan->anio }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_planificacion')
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
            <h3>Modificar Trimestre</h3>
            <button class="modal-close" id="btn-close-edit-modal">&times;</button>
        </div>
        <form id="edit-form" action="{{ old('edit_id') ? route('coordinador.planificacion.trimestres.update', ['id' => old('edit_id')]) : '' }}" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" id="edit-id" name="edit_id" value="{{ old('edit_id') }}">

            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-tag input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-nombre">Nombre del Período / Trimestre</label>
                        <input type="text" id="edit-nombre" name="nombre" autocomplete="off" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror">
                        @error('nombre')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-regular fa-calendar input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-fecha-inicio">Fecha de Inicio</label>
                        <input type="date" id="edit-fecha-inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" class="@error('edit_inicio') val-red @enderror">
                        @error('fecha_inicio')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-regular fa-calendar-check input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-fecha-fin">Fecha de Culminación</label>
                        <input type="date" id="edit-fecha-fin" name="fecha_fin" value="{{ old('fecha_fin') }}" class="@error('fecha_fin') val-red @enderror">
                        @error('fecha_fin')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-solid fa-sitemap input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-planificacion">Planificación Asociada</label>
                        <!-- ACTUALIZADO: Muestra "Título de la Planificación (Año)" -->
                        <select id="edit-planificacion" name="id_planificacion" class="@error('id_planificacion') val-red @enderror">
                            <option value="">Seleccione una planificación...</option>
                            @foreach($planificaciones as $plan)
                                <option value="{{ $plan->id_planificacion }}" {{ old('id_planificacion') == $plan->id_planificacion ? 'selected' : '' }}>
                                    {{ $plan->titulo }} ({{ $plan->anio }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_planificacion')
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
                <h3>Eliminar Trimestre</h3>
                <button type="button" class="modal-close" id="btn-close-delete-modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div class="danger-icon-wrapper">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="delete-warning-text">
                    ¿Está seguro de que desea eliminar el período <strong id="delete-item-name"></strong>?
                </p>
                <p class="delete-sub-text">Esta acción removerá el trimestre de manera permanente.</p>
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
    <script src="{{ asset('assets/js/coordinador/entidades_crud/trimestre.js') }}"></script>
@endpush