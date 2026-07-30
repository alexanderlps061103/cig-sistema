@extends('layouts.app')

@section('title', 'Gestión de Feriados - UNEY')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/entidades_crud/feriado.css') }}">
@endpush

@section('content')
<header class="main-header">
    <div class="header-welcome">
        <h1>Feriados del Calendario</h1>
        <p>Gestión de días no laborables y feriados recurrentes o únicos</p>
    </div>
    <div class="header-date">
        <i class="fa-regular fa-calendar"></i>
        <span id="current-date-display">{{ now()->translatedFormat('F, Y') }}</span>
    </div>
</header>

<!-- Contenedor de Alertas Flotantes (No mueve el HTML) -->
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
            <input type="text" placeholder="Buscar feriado..." class="search-input" id="search-feriado" autocomplete="off">
        </div>

        <div class="filters-wrapper">
            <select class="action-select" id="filter-recurrente">
                <option value="all">Todas las frecuencias</option>
                <option value="yes">Recurrentes</option>
                <option value="no">Fecha única</option>
            </select>

            <button class="btn-primary-action" id="btn-open-create">
                <i class="fa-solid fa-plus"></i> Agregar Feriado
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Frecuencia</th>
                    <th width="150px">Acción</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($registros as $registro)
                <tr class="data-row" 
                    data-descripcion="{{ strtolower($registro->descripcion) }}" 
                    data-fecha="{{ $registro->fecha->format('Y-m-d') }}" 
                    data-recurrente="{{ $registro->recurrente ? 'yes' : 'no' }}">
                    <td><strong>{{ $registro->id }}</strong></td>
                    <td>
                        <div class="table-primary-text">{{ $registro->fecha->format('d/m/Y') }}</div>
                        <span class="table-secondary-text">{{ $registro->fecha->translatedFormat('l') }}</span>
                    </td>
                    <td>{{ $registro->descripcion }}</td>
                    <td>
                        @if($registro->recurrente)
                            <span class="status-badge status-active">Recurrente (Anual)</span>
                        @else
                            <span class="status-badge status-inactive">Fecha Única</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action-edit"
                                onclick="openEditModal('{{ $registro->id }}', '{{ $registro->fecha->format('Y-m-d') }}', '{{ addslashes($registro->descripcion) }}', '{{ $registro->recurrente ? '1' : '0' }}')"
                                title="Editar">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action-delete"
                                onclick="openDeleteModal('{{ $registro->id }}', '{{ addslashes($registro->descripcion) }}')"
                                title="Eliminar">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-state-row" id="empty-row">
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No hay feriados registrados aún.
                    </td>
                </tr>
                @endforelse
                <tr class="empty-state-row" id="no-results-row" style="display: none;">
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        No se encontraron feriados que coincidan con la búsqueda.
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
            <h3>Registrar Feriado</h3>
        </div>
        <form id="create-form" action="{{ route('coordinador.entidades_crud.store', ['modulo' => $modulo]) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-regular fa-calendar input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-fecha">Fecha del Feriado</label>
                        <input type="date" id="create-fecha" name="fecha" value="{{ old('fecha') }}" class="@error('fecha') val-red @enderror">
                        @error('fecha')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row align-start">
                    <i class="fa-solid fa-align-left input-row-icon mt-xs"></i>
                    <div class="input-container">
                        <label for="create-descripcion">Descripción / Motivo</label>
                        <textarea id="create-descripcion" name="descripcion" placeholder="Ej. Día de la Independencia" rows="3" class="@error('descripcion') val-red @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-solid fa-arrows-spin input-row-icon"></i>
                    <div class="input-container">
                        <label for="create-recurrente">Frecuencia</label>
                        <select id="create-recurrente" name="recurrente">
                            <option value="0" {{ old('recurrente') == '0' ? 'selected' : '' }}>Fecha Única (Solo este año)</option>
                            <option value="1" {{ old('recurrente') == '1' ? 'selected' : '' }}>Recurrente (Se repite anualmente)</option>
                        </select>
                        @error('recurrente')
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
            <h3>Modificar Feriado</h3>
        </div>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group-row">
                    <i class="fa-regular fa-calendar input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-fecha">Fecha del Feriado</label>
                        <input type="date" id="edit-fecha" name="fecha" value="{{ old('fecha') }}" class="@error('fecha') val-red @enderror">
                        @error('fecha')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row align-start">
                    <i class="fa-solid fa-align-left input-row-icon mt-xs"></i>
                    <div class="input-container">
                        <label for="edit-descripcion">Descripción / Motivo</label>
                        <textarea id="edit-descripcion" name="descripcion" rows="3" class="@error('descripcion') val-red @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <span class="error-input-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group-row">
                    <i class="fa-solid fa-arrows-spin input-row-icon"></i>
                    <div class="input-container">
                        <label for="edit-recurrente">Frecuencia</label>
                        <select id="edit-recurrente" name="recurrente">
                            <option value="0" {{ old('recurrente') == '0' ? 'selected' : '' }}>Fecha Única (Solo este año)</option>
                            <option value="1" {{ old('recurrente') == '1' ? 'selected' : '' }}>Recurrente (Se repite anualmente)</option>
                        </select>
                        @error('recurrente')
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

<!-- MODAL DE ELIMINACIÓN -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal-container modal-danger-layout">
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h3>Eliminar Feriado</h3>
            </div>
            <div class="modal-body text-center">
                <div class="danger-icon-wrapper">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="delete-warning-text">
                    ¿Está seguro de que desea eliminar el feriado <strong id="delete-item-name"></strong>?
                </p>
                <p class="delete-sub-text">Esta acción no se puede deshacer.</p>
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
    <script src="{{ asset('assets/js/coordinador/entidades_crud/feriado.js') }}"></script>
@endpush