@extends('layouts.app')

@section('title', 'Gestionar Cargos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/estructura/cargo.css') }}">
@endpush

@section('content')
<header class="main-header">
    <div class="header-welcome">
        <h1>Gestionar Cargos</h1>
        <p>Administración de puestos institucionales</p>
    </div>
</header>

{{-- Alertas --}}
<div class="alerts-container">
    @if(session('success'))
        <div class="alert-message success-alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
</div>

<section class="crud-container">
    <div class="table-actions-bar">
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="search-cargo" class="search-input" placeholder="Buscar...">
        </div>
        <button class="btn-primary-action" id="btn-open-create">
            <i class="fa-solid fa-plus"></i> Agregar Cargo
        </button>
    </div>

    <div class="table-responsive">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cargos as $c)
                <tr class="data-row" data-nombre="{{ $c->nombre }}">
                    <td>{{ $c->id }}</td>
                    <td class="table-primary-text">{{ $c->nombre }}</td>
                    <td>
                        <span class="status-badge {{ $c->estado ? 'status-active' : 'status-inactive' }}">
                            {{ $c->estado ? 'Habilitado' : 'Inhabilitado' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action-edit" onclick="openEditModal('{{ $c->id }}', '{{ $c->nombre }}')">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action-delete" onclick="openToggleModal('{{ $c->id }}', '{{ $c->nombre }}', {{ $c->estado ? 'true' : 'false' }})">
                                <i class="fa-solid {{ $c->estado ? 'fa-ban' : 'fa-check-circle' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<!-- MODAL CREAR -->
<div class="modal-overlay" id="create-modal">
    <div class="modal-container">
        <div class="modal-header"><h3>Nuevo Cargo</h3></div>
        <form action="{{ route('rector.cargos.store') }}" method="POST" id="create-form">
            @csrf
            <div class="modal-body">
                <div class="input-container">
                    <label>Nombre del Cargo</label>
                    <input type="text" name="nombre" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" id="btn-cancel-create">Cancelar</button>
                <button type="submit" class="modal-btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal-overlay" id="edit-modal">
    <div class="modal-container">
        <div class="modal-header"><h3>Editar Cargo</h3></div>
        <form method="POST" id="edit-form">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="input-container">
                    <label>Nombre del Cargo</label>
                    <input type="text" name="nombre" id="edit-nombre-cargo" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn btn-secondary" id="btn-cancel-edit">Cerrar</button>
                <button type="submit" class="modal-btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TOGGLE (ELIMINAR) -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal-container modal-danger-layout">
        <form id="delete-form" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body text-center">
                <h3 id="toggle-title">¿Desea cambiar el estado?</h3>
                <p>Cargo: <strong id="delete-item-name"></strong></p>
            </div>
            <div class="modal-footer" style="justify-content: center;">
                <button type="button" class="modal-btn btn-secondary" id="btn-cancel-delete">Cancelar</button>
                <button type="submit" class="modal-btn" id="btn-confirm-toggle">Confirmar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/estructura/cargo.js') }}"></script>
@endpush
