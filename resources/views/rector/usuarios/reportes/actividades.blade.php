@extends('layouts.app')

@section('title', 'Reporte de Actividades - Rector')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/reportes.css') }}">
@endpush

@section('content')
    <header class="main-header">
        <div class="header-welcome">
            <h1>Reporte de Actividades</h1>
            <p>Estadísticas de actividades formativas</p>
        </div>
    </header>

    <div class="placeholder-card">
        <div class="placeholder-icon">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <h2>Próximamente</h2>
        <p>Esta sección mostrará gráficos y datos sobre las actividades registradas en el sistema.</p>
    </div>
@endsection
