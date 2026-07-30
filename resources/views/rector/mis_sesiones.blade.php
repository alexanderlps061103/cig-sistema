@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/personal.css') }}">
@endpush

@section('content')
<div class="main-header">
    <h1><i class="fa-solid fa-chalkboard"></i> Mis Sesiones Asignadas</h1>
    <p class="text-muted">Como docente/ponente, estas son las clases que debes impartir.</p>
</div>

<div class="session-grid">
    @forelse($sesiones as $s)
    <div class="session-card">
        <div class="date-badge">
            <span class="day">{{ $s->start_at->format('d') }}</span>
            <span class="month">{{ $s->start_at->translatedFormat('M') }}</span>
        </div>

        <span class="activity-name">{{ $s->actividad->nombre }}</span>
        <h3>{{ $s->tema }}</h3>

        <div class="info-row">
            <i class="fa-solid fa-clock"></i>
            <span>{{ $s->start_at->format('H:i') }} - {{ $s->end_at->format('H:i') }}</span>
        </div>
        <div class="info-row">
            <i class="fa-solid fa-location-dot"></i>
            <span>{{ $s->lugar }}</span>
        </div>

        <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
            <button class="filter-pill active" style="border:none; cursor:pointer; font-size:0.75rem;" onclick="verQrAsistencia('{{ $s->qr_token }}')">
                <i class="fa-solid fa-qrcode"></i> Mostrar QR
            </button>
            <a href="#" class="filter-pill" style="font-size:0.75rem; text-decoration:none;">
                <i class="fa-solid fa-users"></i> Ver Inscritos
            </a>
        </div>
    </div>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
        <i class="fa-solid fa-calendar-day" style="font-size: 3rem; color: #e2e8f0; margin-bottom: 1rem;"></i>
        <p class="text-muted">No tienes sesiones asignadas para dictar próximamente.</p>
    </div>
    @endforelse
</div>

{{-- MODAL QR --}}
<div id="modalQrSesion" class="modal-overlay">
    <div class="modal-content" style="width: 350px; text-align: center;">
        <button class="modal-close" style="float:right; border:none; background:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        <h3 style="margin-bottom: 1rem;">QR de Asistencia</h3>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1.5rem;">Presenta este código a los estudiantes para registrar su entrada.</p>

        <div id="qrContainer" style="background: white; padding: 1rem; border: 1px solid #eee; border-radius: 1rem;">
            {{-- Aquí se inserta el QR por JS --}}
        </div>

        <button class="filter-pill active" style="width:100%; margin-top:1.5rem; border:none;" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Imprimir Código
        </button>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/rector/personal.js') }}"></script>
@endpush
