@extends('layouts.app')

@section('title', 'Gestión de Carreras - SIACSACIG')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/estructura/carrera.css') }}">
    <style>
        /* Estilos para la X de cerrar los modales */
        .btn-close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--color-text-muted);
            cursor: pointer;
            transition: color 0.3s;
            line-height: 1;
        }
        .btn-close-modal:hover {
            color: var(--color-danger-text, #ef4444);
        }
        /* Ajuste para que el botón de cerrar quede a la derecha */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
    </style>
@endpush

@section('content')
<!-- Cabecera de Contenido -->
<header class="main-header">
    <div class="header-welcome">
        <h1>Gestión de Carreras</h1>
        <p>Configuración y administración de programas de estudio ofertados</p>
    </div>
    <div class="header-date">
        <i class="fa-regular fa-calendar"></i>
        <span>{{ now()->translatedFormat('F, Y') }}</span>
    </div>
</header>

<!-- Contenedor de Alertas -->
<div class="alerts-container">
    @if(session('success'))
        <div class="alert-message success-alert">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-message error-alert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <ul style="margin: 0; list-style: none; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<!-- Contenedor Principal -->
<section class="crud-container">
    <div class="table-actions-bar">
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" placeholder="Buscar carrera..." class="search-input" id="search-carrera">
        </div>

        <div class="filters-wrapper">
            <select class="action-select" id="filter-status">
                <option value="all">Todos los estados</option>
                <option value="1">Habilitadas</option>
                <option value="0">Inhabilitadas</option>
            </select>

            <button class="btn-primary-action" id="btn-open-create">
                <i class="fa-solid fa-plus"></i> Agregar Carrera
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th width="80px">ID</th>
                    <th>Carrera / PNF</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th width="150px">Acción</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @forelse($registros as $c)
                <tr class="data-row"
                    data-nombre="{{ strtolower($c->nombre) }}"
                    data-descripcion="{{ strtolower($c->descripcion ?? '') }}"
                    data-estado="{{ $c->estado ? '1' : '0' }}">
                    <td><strong>{{ $c->id }}</strong></td>
                    <td><div class="table-primary-text">{{ $c->nombre }}</div></td>
                    <td class="table-secondary-text">{{ Str::limit($c->descripcion, 60) ?? 'Sin descripción.' }}</td>
                    <td>
                        <span class="status-badge {{ $c->estado ? 'status-active' : 'status-inactive' }}">
                            {{ $c->estado ? 'Habilitado' : 'Inhabilitado' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action-edit"
                                onclick="openEditModal('{{ $c->id }}', '{{ addslashes($c->nombre) }}', '{{ addslashes($c->descripcion ?? '') }}')">
                                <i class="fa-solid fa-pen"></i> Editar
                            </button>
                            <button class="btn-action-delete"
                                onclick="openToggleModal('{{ $c->id }}', '{{ addslashes($c->nombre) }}', {{ $c->estado ? 'true' : 'false' }})">
                                <i class="fa-solid fa-power-off"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No hay carreras registradas.</td>
                </tr>
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
        <div class="modal-header">
            <h3>Registrar Carrera</h3>
            <button type="button" class="btn-close-modal" onclick="closeAllModals()">&times;</button>
        </div>
        <form action="{{ route('rector.carreras.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group-row">
                    <div class="input-container">
                        <label>Nombre de la Carrera / PNF</label>
                        <input type="text" name="nombre" placeholder="Ej: Ciencias Alimentarias" required>
                    </div>
                </div>
                <div class="form-group-row align-start">
                    <div class="input-container">
                        <label>Descripción</label>
                        <textarea name="descripcion" rows="3" placeholder="Breve descripción del programa..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" onclick="closeAllModals()">Cancelar</button>
                <button type="submit" class="modal-btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal-overlay" id="edit-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Modificar Carrera</h3>
            <button type="button" class="btn-close-modal" onclick="closeAllModals()">&times;</button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group-row">
                    <div class="input-container">
                        <label>Nombre de la Carrera</label>
                        <input type="text" id="edit-nombre" name="nombre" required>
                    </div>
                </div>
                <div class="form-group-row align-start">
                    <div class="input-container">
                        <label>Descripción</label>
                        <textarea id="edit-descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" onclick="closeAllModals()">Cerrar</button>
                <button type="submit" class="modal-btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TOGGLE (ESTADO) -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal-container modal-danger-layout">
        <form id="toggle-form" method="POST">
            @csrf @method('PATCH')
            <div class="modal-header">
                <h3 id="toggle-title">Cambiar Estado</h3>
                <button type="button" class="btn-close-modal" onclick="closeAllModals()">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div class="danger-icon-wrapper"><i class="fa-solid fa-circle-exclamation"></i></div>
                <p class="delete-warning-text">¿Desea cambiar el estado de <strong id="toggle-item-name"></strong>?</p>
            </div>
            <div class="modal-footer" style="justify-content:center;">
                <button type="button" class="modal-btn btn-secondary" onclick="closeAllModals()">Cancelar</button>
                <button type="submit" class="modal-btn btn-danger" id="btn-confirm-toggle">Confirmar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Función global para cerrar todos los modales (usada por la X)
        function closeAllModals() {
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    </script>
    <script src="{{ asset('assets/js/estructura/carrera.js') }}"></script>
@endpush