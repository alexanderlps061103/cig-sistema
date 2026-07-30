@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/personal.css') }}">
@endpush

@section('content')
<div class="main-header">
    <h1><i class="fa-solid fa-certificate"></i> Mis Certificados</h1>
    <p class="text-muted">Aquí encontrarás los reconocimientos obtenidos por tu participación académica.</p>
</div>

<div class="table-card" style="padding: 1.5rem;">
    <div class="cert-list">
        @forelse($certificados as $c)
        <div class="cert-item">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div class="cert-icon">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div class="cert-info">
                    <h4>{{ $c->actividad->nombre ?? 'Actividad General' }}</h4>
                    <p>
                        Tipo: <strong>{{ ucfirst($c->tipo) }}</strong> |
                        Fecha: {{ $c->fecha_emision->format('d/m/Y') }}
                    </p>
                    <span class="verify-code">Cód: {{ $c->codigo_verificacion }}</span>
                </div>
            </div>

            <div class="actions">
                <a href="{{ asset($c->archivo) }}" target="_blank" class="filter-pill active" style="text-decoration:none;">
                    <i class="fa-solid fa-download"></i> Descargar PDF
                </a>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 3rem;">
            <p class="text-muted">Aún no has recibido certificados.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
