@extends('layouts.app')

@section('title', 'Docentes - Rector')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/docentes.css') }}">
@endpush

@section('content')
    <header class="main-header">
        <div class="header-welcome">
            <h1>Docentes</h1>
            <p>Listado del personal docente activo</p>
        </div>
    </header>

    {{-- Barra de búsqueda --}}
    <form method="GET" action="{{ route('rector.docentes.index') }}" class="search-bar">
        <div class="search-input-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" placeholder="Buscar por nombre o apellido"
                   value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary-action">Buscar</button>
        @if(request('search'))
            <a href="{{ route('rector.docentes.index') }}" class="btn btn-secondary-action">Limpiar</a>
        @endif
    </form>

    {{-- Tabla de docentes --}}
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Correo electrónico</th>
                    <th>Especialidad / Área</th>
                </tr>
            </thead>
            <tbody>
                @forelse($docentes as $docente)
                    @php $persona = $docente->persona; @endphp
                    <tr>
                        <td>
                            <span class="user-name">{{ $persona->nombre_completo ?? 'Sin nombre' }}</span>
                        </td>
                        <td>{{ $persona->cedula ?? '—' }}</td>
                        <td>{{ $persona->usuario->email ?? 'Sin correo' }}</td>
                        <td>{{ $docente->especialidad ?? 'No definida' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-row">No se encontraron docentes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            {{ $docentes->links() }}
        </div>
    </div>
@endsection
