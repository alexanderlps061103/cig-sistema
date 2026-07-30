@extends('layouts.app')

@section('title', 'Gestionar Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/usuarios.css') }}">
@endpush

@section('content')
    <header class="main-header">
        <div class="header-welcome">
            <h1>Gestionar Usuarios</h1>
            <p>Panel administrativo de perfiles, roles y credenciales</p>
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
        @if(session('error'))
            <div class="alert-message error-alert">
                <i class="fa-solid fa-circle-xmark"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <section class="crud-container">
        <div class="table-actions-bar">
            <form action="{{ route('rector.usuarios.index') }}" method="GET" class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="buscar" placeholder="Cédula o nombre..." class="search-input" value="{{ request('buscar') }}">
            </form>

            <div class="filters-wrapper">
                <form action="{{ route('rector.usuarios.index') }}" method="GET">
                    <select name="rol" class="action-select" onchange="this.form.submit()">
                        <option value="">Todos los Roles</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->nombre }}" {{ request('rol') == $rol->nombre ? 'selected' : '' }}>
                                {{ ucfirst($rol->nombre) }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <button class="btn-primary-action" onclick="openCreateModal()">
                    <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>Datos Personales</th>
                        <th>Cédula</th>
                        <th>Profesión / Carrera</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th width="150px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $persona)
                        <tr class="data-row">
                            {{-- Columna 1: Datos Personales --}}
                            <td>
                                <div class="table-primary-text">{{ $persona->nombres }} {{ $persona->apellidos }}</div>
                                <span class="table-secondary-text">{{ $persona->usuario->email ?? 'Sin email' }}</span>
                            </td>

                            {{-- Columna 2: Cédula --}}
                            <td><strong>{{ $persona->cedula }}</strong></td>

                            {{-- Columna 3: Profesión / Carrera --}}
                            <td>
                                @if($persona->docentes)
                                    <div class="table-primary-text">{{ $persona->docentes->profesion->nombre ?? 'Profesión N/A' }}</div>
                                    @if($persona->empleado)
                                        <span class="table-secondary-text">Cargo: {{ $persona->empleado->cargo->nombre ?? 'Sin Cargo' }}</span>
                                    @endif
                                @elseif($persona->estudiante)
                                    <div class="table-primary-text">{{ $persona->estudiante->carrera->nombre ?? 'N/A' }}</div>
                                    <span class="table-secondary-text">Estudiante</span>
                                @else
                                    <span class="table-secondary-text">Sin datos profesionales</span>
                                @endif
                            </td>

                            {{-- Columna 4: Rol --}}
                            <td>
                                @foreach($persona->roles as $rol)
                                    <span class="status-badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe;">
                                        {{ ucfirst($rol->nombre) }}
                                    </span>
                                @endforeach
                            </td>

                            {{-- Columna 5: Estado --}}
                            <td>
                                <span class="status-badge {{ ($persona->usuario && $persona->usuario->activo) ? 'status-active' : 'status-inactive' }}">
                                    {{ ($persona->usuario && $persona->usuario->activo) ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            {{-- Columna 6: Acciones --}}
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action-edit" onclick="openEditModal({{ json_encode($persona) }})" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn-action-delete" onclick="openStatusModal('{{ $persona->id }}', '{{ $persona->nombres }}')" title="Cambiar Estado">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5">No se encontraron registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            {{ $usuarios->links() }}
        </div>
    </section>

    <!-- MODAL FORMULARIO -->
    <div class="modal-overlay" id="user-modal">
        <div class="modal-container" style="max-width: 700px;">
            <div class="modal-header"><h3 id="modal-title">Usuario</h3></div>
            <form id="user-form" method="POST">
                @csrf
                <div id="method-container"></div>

                <div class="modal-body">
                    <div class="form-group-row">
                        <div class="input-container">
                            <label>Cédula</label>
                            <input type="text" name="cedula" id="form-cedula" required>
                        </div>
                        <div class="input-container">
                            <label>Email</label>
                            <input type="email" name="email" id="form-email" required>
                        </div>
                    </div>

                    <div class="form-group-row">
                        <div class="input-container">
                            <label>Nombres</label>
                            <input type="text" name="nombres" id="form-nombres" required>
                        </div>
                        <div class="input-container">
                            <label>Apellidos</label>
                            <input type="text" name="apellidos" id="form-apellidos" required>
                        </div>
                    </div>

                    <div class="form-group-row">
                        <div class="input-container">
                            <label>Rol Principal</label>
                            <select name="rol_id" id="form-rol-id" required onchange="handleRoleChange()">
                                <option value="">Seleccione un rol</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}" data-nombre="{{ strtolower($rol->nombre) }}">
                                        {{ ucfirst($rol->nombre) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-container">
                            <label>Contraseña (Opcional)</label>
                            <input type="password" name="password" id="form-password">
                        </div>
                    </div>

                    <div class="form-group-row">
                        <div class="input-container">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" id="form-telefono">
                        </div>
                        <div class="input-container">
                            <label>Sexo</label>
                            <select name="sexo" id="form-sexo">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sección Estudiante -->
                    <div id="section-estudiante" class="profile-section-container" style="display:none;">
                        <hr>
                        <div class="form-group-row">
                            <div class="input-container">
                                <label>Carrera</label>
                                <select name="carrera_id" id="form-carrera-id">
                                    <option value="">Seleccione carrera</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Empleado (Profesión y Cargo siempre visibles para Docente/Coord/Rector) -->
                    <div id="section-empleado" class="profile-section-container" style="display:none;">
                        <hr>
                        <div class="form-group-row">
                            <div class="input-container">
                                <label>Profesión</label>
                                <select name="profesion_id" id="form-profesion-id">
                                    <option value="">Seleccione profesión</option>
                                    @foreach($profesiones as $prof)
                                        <option value="{{ $prof->id }}">{{ $prof->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-container">
                                <label>Cargo</label>
                                <select name="cargo_id" id="form-cargo-id">
                                    <option value="">Seleccione cargo</option>
                                    @foreach($cargos as $cargo)
                                        <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="modal-btn btn-secondary" onclick="closeUserModal()">Cancelar</button>
                    <button type="submit" class="modal-btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL STATUS -->
    <div class="modal-overlay" id="status-modal">
        <div class="modal-container modal-danger-layout">
            <form id="status-form" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body text-center">
                    <div class="danger-icon-wrapper"><i class="fa-solid fa-sync"></i></div>
                    <p class="delete-warning-text">¿Cambiar estado de <strong id="status-user-name"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn btn-secondary" onclick="closeStatusModal()">No, cancelar</button>
                    <button type="submit" class="modal-btn btn-danger">Sí, cambiar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/rector/usuarios.js') }}"></script>
@endpush
