@extends('layouts.app')

@section('title', 'Solicitudes de Empleo - Rector')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/solicitudes.css') }}">
@endpush

@section('content')
    <header class="main-header">
        <div class="header-welcome">
            <h1>Solicitudes de Empleo</h1>
            <p>Revisión de aspirantes a docentes y personal</p>
        </div>
    </header>

    {{-- Filtros por estado --}}
    <div class="filter-bar">
        <a href="{{ route('rector.solicitudes.index') }}"
           class="filter-pill {{ !request('estado') ? 'active' : '' }}">Todas</a>
        <a href="{{ route('rector.solicitudes.index', ['estado' => 'pendiente']) }}"
           class="filter-pill {{ request('estado') === 'pendiente' ? 'active' : '' }}">Pendientes</a>
        <a href="{{ route('rector.solicitudes.index', ['estado' => 'aprobada']) }}"
           class="filter-pill {{ request('estado') === 'aprobada' ? 'active' : '' }}">Aprobadas</a>
        <a href="{{ route('rector.solicitudes.index', ['estado' => 'rechazada']) }}"
           class="filter-pill {{ request('estado') === 'rechazada' ? 'active' : '' }}">Rechazadas</a>
    </div>

    {{-- Tabla de solicitudes --}}
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Solicitante</th>
                    <th>Cédula</th>
                    <th>Fecha de solicitud</th>
                    <th>Estado</th>
                    <th>Currículum</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $solicitud)
                    @php $persona = $solicitud->persona; @endphp
                    <tr>
                        <td>
                            <span class="user-name">{{ $persona->nombre_completo ?? 'Sin nombre' }}</span>
                        </td>
                        <td>{{ $persona->cedula ?? '—' }}</td>
                        <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="estado-badge estado-{{ $solicitud->estado }}">
                                {{ ucfirst($solicitud->estado) }}
                            </span>
                        </td>
                        <td>
                            @if($solicitud->persona && $solicitud->persona->curriculums->count())
                                @foreach($solicitud->persona->curriculums as $curriculum)
                                    <a href="{{ $curriculum->archivo_url ?? '#' }}" target="_blank" class="curriculum-link">
                                        <i class="fa-solid fa-file-pdf"></i> Ver currículum
                                    </a>
                                    @if(!$loop->last)<br>@endif
                                @endforeach
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">No se encontraron solicitudes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            {{ $solicitudes->links() }}
        </div>
    </div>
@endsection
