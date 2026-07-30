@extends('layouts.app')

@section('title', 'Gestión de Firmas - UNEY')

@push('styles')
    <!-- CSS Propio de esta vista -->
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/entidades_crud/firma.css') }}">
@endpush

@section('content')
<!-- Cabecera de Contenido -->
<header class="main-header">
    <div class="header-welcome">
        <h1>Firmas Autorizadas</h1>
        <p>Gestión y configuración de firmas digitales para la emisión de certificaciones</p>
    </div>
    <div class="header-date">
        <i class="fa-regular fa-calendar"></i>
        <span id="current-date-display">{{ now()->translatedFormat('F, Y') }}</span>
    </div>
</header>

<!-- Contenedor de Alertas Flotantes (Fijo por CSS para no mover la estructura HTML) -->
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

    <!-- Barra Superior de Acciones y Filtros -->
    <div class="table-actions-bar">
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" placeholder="Buscar firma..." class="search-input" id="search-firma" autocomplete="off">
        </div>

        <div class="filters-wrapper">
            <select class="action-select" id="filter-status">
                <option value="all">Todos los estados</option>
                <option value="active">Habilitadas</option>
                <option value="inactive">Inhabilitadas</option>
            </select>

            <button class="btn-primary-action" id="btn-open-create">
                <i class="fa-solid fa-plus"></i> Agregar Firma
            </button>
        </div>
    </div>

    <!-- Tabla de Datos -->
    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Vista Previa</th>
                    <th>Estado</th>
                    <th width="150px">Acción</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($registros as $registro)
                <tr class="data-row" 
                    data-nombre="{{ strtolower($registro->nombre) }}" 
                    data-estado="{{ $registro->estado ? 'active' : 'inactive' }}">
                    <td><strong>{{ $registro->id_firma }}</strong></td>
                    <td>
                        <div class="table-primary-text">{{ $registro->nombre }}</div>
                        <span class="table-secondary-text">Cod: FRM-{{ str_pad($registro->id_firma, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td>
                        @if($registro->imagen)
                            <div class="table-image-wrapper">
                                <img src="{{ asset('storage/' . $registro->imagen) }}" alt="Firma de {{ $registro->nombre }}" class="table-preview-img">
                            </div>
                        @else
                            <span class="table-secondary-text">Sin imagen cargada</span>
                        @endif
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
                                title="Editar"
                                onclick="openEditModal('{{ $registro->id_firma }}', '{{ addslashes($registro->nombre) }}', '{{ $registro->imagen ? asset('storage/' . $registro->imagen) : '' }}', '{{ $registro->estado ? 'active' : 'inactive' }}')">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action-delete"
                                onclick="openDeleteModal('{{ $registro->id_firma }}', '{{ addslashes($registro->nombre) }}')">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-state-row" id="empty-row">
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No hay firmas registradas aún.
                    </td>
                </tr>
                @endforelse
                <!-- Fila defensiva para búsquedas vacías en JS -->
                <tr class="empty-state-row" id="no-results-row" style="display: none;">
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No se encontraron firmas que coincidan con la búsqueda.
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
            <h3>Registrar Firma</h3>
        </div>
        <form id="create-form" action="{{ route('coordinador.entidades_crud.store', ['modulo' => $modulo]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-user input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-nombre">Nombre del Firmante</label>
                        <input type="text" id="create-nombre" name="nombre" placeholder="Ej. Dr. Juan Pérez" autocomplete="off" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror">
                        @error('nombre')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row align-start">
                    <i class="fa-solid fa-image input-row-icon mt-xs"></i>
                    <div class="input-container">
                        <label for="create-imagen">Imagen de la Firma (.png, .jpg, .svg)</label>
                        <input type="file" id="create-imagen" name="imagen" accept="image/*" class="@error('imagen') val-red @enderror">
                        @error('imagen')
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
            <h3>Modificar Firma</h3>
        </div>
        <form id="edit-form" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-solid fa-user input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-nombre">Nombre del Firmante</label>
                        <input type="text" id="edit-nombre" name="nombre" autocomplete="off" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror">
                        @error('nombre')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row align-start">
                    <i class="fa-solid fa-image input-row-icon mt-xs"></i>
                    <div class="input-container">
                        <label for="edit-imagen">Reemplazar Imagen de la Firma (Opcional)</label>
                        <input type="file" id="edit-imagen" name="imagen" accept="image/*" class="@error('imagen') val-red @enderror">
                        <div id="edit-image-preview-container" class="image-preview-box" style="display: none; margin-top: 1rem;">
                            <span class="table-secondary-text" style="display: block; margin-bottom: 0.5rem;">Firma actual cargada:</span>
                            <img id="edit-preview" src="" alt="Firma actual" class="modal-preview-img">
                        </div>
                        @error('imagen')
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
                <h3>Inhabilitar Firma</h3>
            </div>
            <div class="modal-body text-center">
                <div class="danger-icon-wrapper">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="delete-warning-text">
                    ¿Está seguro de que desea inhabilitar la firma de <strong id="delete-item-name"></strong>?
                </p>
                <p class="delete-sub-text">Esta acción cambiará el estado de la firma a inhabilitada.</p>
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
    <script src="{{ asset('assets/js/coordinador/entidades_crud/firma.js') }}"></script>
@endpush