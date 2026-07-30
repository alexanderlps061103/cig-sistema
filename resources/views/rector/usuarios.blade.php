@extends('layouts.app')

@section('title', 'Gestionar Usuarios - Rector')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/usuarios.css') }}">
@endpush

@section('content')
    <header class="main-header">
        <div class="header-welcome">
            <h1>Gestionar Usuarios</h1>
            <p>Todos los usuarios registrados en el sistema</p>
        </div>
    </header>

    {{-- Barra de búsqueda --}}
    <form method="GET" action="{{ route('rector.usuarios.index') }}" class="search-bar">
        <div class="search-input-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" placeholder="Buscar por nombre, apellido, cédula o email"
                   value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary-action">Buscar</button>
        @if(request('search'))
            <a href="{{ route('rector.usuarios.index') }}" class="btn btn-secondary-action">Limpiar</a>
        @endif
    </form>

    {{-- Tabla de usuarios --}}
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Correo electrónico</th>
                    <th>Roles</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    @php $persona = $usuario->persona; @endphp
                    <tr>
                        <td>
                            <div class="user-name">{{ $persona->nombre_completo ?? 'Sin persona asociada' }}</div>
                        </td>
                        <td>{{ $persona->cedula ?? '—' }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @if($persona && $persona->roles->count())
                                @foreach($persona->roles as $rol)
                                    <span class="badge badge-rol">{{ $rol->nombre }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Sin rol</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $usuario->activo ? 'active' : 'inactive' }}">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            @if($persona)
                                <form method="POST" action="{{ route('rector.usuarios.toggle', $persona) }}" class="toggle-form">
                                    @csrf
                                    <button type="submit" class="btn-toggle {{ $usuario->activo ? 'deactivate' : 'activate' }}"
                                            title="{{ $usuario->activo ? 'Desactivar usuario' : 'Activar usuario' }}">
                                        <i class="fa-solid {{ $usuario->activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">No se encontraron usuarios.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            {{ $usuarios->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/rector/usuarios.js') }}"></script>
@endpush
