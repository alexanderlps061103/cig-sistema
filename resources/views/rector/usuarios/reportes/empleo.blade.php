@extends('layouts.app')

@section('title', 'Reporte de Empleo - Rector')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/reportes.css') }}">
@endpush

@section('content')
    <header class="main-header">
        <div class="header-welcome">
            <h1>Reporte de Empleo</h1>
            <p>Estadísticas de solicitudes y docentes activos</p>
        </div>
    </header>

    {{-- KPI de empleo --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon icon-blue"><i class="fa-solid fa-file-circle-plus"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Total Solicitudes</span>
                <span class="kpi-value">{{ $totalSolicitudes }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-amber"><i class="fa-solid fa-clock"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Pendientes</span>
                <span class="kpi-value">{{ $pendientes }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-green"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Aprobadas</span>
                <span class="kpi-value">{{ $aprobadas }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-red"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Rechazadas</span>
                <span class="kpi-value">{{ $rechazadas }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-purple"><i class="fa-solid fa-chalkboard-user"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Docentes Activos</span>
                <span class="kpi-value">{{ $docentesActivos }}</span>
            </div>
        </div>
    </div>
@endsection
