@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rector/reportes_empleo.css') }}">
@endpush

@section('content')
<div class="main-header">
    <h1><i class="fa-solid fa-chart-pie"></i> Estadísticas de Empleo</h1>
    <p class="text-muted">Análisis de postulaciones y distribución de especialidades docentes.</p>
</div>

{{-- Cuadros de Resumen --}}
<div class="stats-container">
    <div class="stat-box pending">
        <h3>Pendientes</h3>
        <span class="number">{{ $statsEstado['pendientes'] }}</span>
    </div>
    <div class="stat-box approved">
        <h3>Aprobadas</h3>
        <span class="number">{{ $statsEstado['aprobadas'] }}</span>
    </div>
    <div class="stat-box rejected">
        <h3>Rechazadas</h3>
        <span class="number">{{ $statsEstado['rechazadas'] }}</span>
    </div>
</div>

<div class="charts-grid">
    {{-- Gráfico Circular de Estados --}}
    <div class="chart-card">
        <h4>Distribución de Solicitudes</h4>
        <canvas id="chartEstados"></canvas>
    </div>

    {{-- Gráfico de Barras por Profesión --}}
    <div class="chart-card">
        <h4>Postulantes por Especialidad</h4>
        <canvas id="chartProfesiones"></canvas>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos para Gráfico de Estados
        const ctxEstados = document.getElementById('chartEstados').getContext('2d');
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Aprobadas', 'Rechazadas'],
                datasets: [{
                    data: [{{ $statsEstado['pendientes'] }}, {{ $statsEstado['aprobadas'] }}, {{ $statsEstado['rechazadas'] }}],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });

        // Datos para Gráfico de Profesiones
        const ctxProfesiones = document.getElementById('chartProfesiones').getContext('2d');
        new Chart(ctxProfesiones, {
            type: 'bar',
            data: {
                labels: {!! json_encode($porProfesion->pluck('nombre')) !!},
                datasets: [{
                    label: 'Cantidad de Postulantes',
                    data: {!! json_encode($porProfesion->pluck('total')) !!},
                    backgroundColor: '#6366f1',
                    borderRadius: 8
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { display: false } }
            }
        });
    </script>
@endpush