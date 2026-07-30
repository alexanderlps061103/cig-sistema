@extends('layouts.app')

@section('title', 'Panel de Usuario')

@push('styles')
    <!-- Vinculación del archivo CSS separado -->
    <link rel="stylesheet" href="{{ asset('assets/css/publico_general/dashboardPublico.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/menu/menu.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    
    <!-- Alertas Flotantes Absolutas -->
    <div class="alerts-container">
        @if(session('success'))
            <div class="alert-message success-alert">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-message error-alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <ul style="margin: 0; padding-left: 1rem; list-style-type: none;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Banner de Bienvenida -->
    <header class="welcome-banner">
        <h1>¡Hola, {{ auth()->user()->persona->nombres }}!</h1>
        <p>Este es tu panel de control de actividades académicas de la UNEY.</p>
    </header>

    <!-- Buscador y Título de Sección -->
    <section class="section-title-bar">
        <h2>Actividades del mes de {{ $now->translatedFormat('F Y') }}</h2>
        <div class="search-filter-wrapper">
            <input type="text" id="activity-search-input" placeholder="Buscar actividad..." class="search-input">
        </div>
    </section>

    <!-- Grid de Actividades / Estados Vacíos -->
    <section class="activities-section-wrapper">
        @if(count($actividades) > 0)
            <div class="activities-grid" id="activities-container">
                @foreach($actividades as $act)
                    @php
                        $estaInscrito = in_array($act->id_actividad, $inscripcionesUsuario);
                    @endphp
                    <!-- Card de Actividad estructurada para JS -->
                    <div class="activity-card" 
                         data-id="{{ $act->id_actividad }}"
                         data-nombre="{{ $act->nombre }}"
                         data-descripcion="{{ $act->descripcion }}"
                         data-fecha="{{ $act->fecha->translatedFormat('d \d\e F, Y') }}"
                         data-horario="{{ \Carbon\Carbon::parse($act->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($act->hora_fin)->format('h:i A') }}"
                         data-ubicacion="{{ $act->salon->nombre ?? 'Por definir' }} (Capacidad: {{ $act->salon->capacidad ?? 'N/A' }})"
                         data-modalidad="{{ $act->modalidadRelacion->nombre_modalidad ?? 'N/A' }}"
                         data-tipo="{{ $act->tipo->nombre ?? 'Actividad' }}"
                         data-inscrito="{{ $estaInscrito ? 'true' : 'false' }}">
                        
                        <div class="activity-card-header">
                            <span class="activity-tag">{{ $act->tipo->nombre ?? 'Actividad' }}</span>
                            <h3 class="activity-title">{{ $act->nombre }}</h3>
                        </div>
                        <div class="activity-card-body">
                            <div class="info-row">
                                <i class="fa-regular fa-calendar-days"></i>
                                <span>Fecha: {{ $act->fecha->translatedFormat('d \d\e F, Y') }}</span>
                            </div>
                            <div class="info-row">
                                <i class="fa-regular fa-clock"></i>
                                <span>Horario: {{ \Carbon\Carbon::parse($act->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($act->hora_fin)->format('h:i A') }}</span>
                            </div>
                        </div>
                        <div class="activity-card-footer">
                            <button type="button" class="btn-action btn-view-details">
                                <i class="fa-solid fa-eye"></i> Ver detalles
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div id="no-results-row" class="no-activities" style="display: none;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <h3>No se encontraron actividades con ese nombre</h3>
            </div>
        @else
            <div class="no-activities">
                <i class="fa-solid fa-calendar-xmark"></i>
                <h3>No hay actividades disponibles para este mes</h3>
            </div>
        @endif
    </section>

</div>

{{-- ==================== MODALES ==================== --}}

{{-- 1. MODAL DETALLES DE LA ACTIVIDAD --}}
<div id="activity-details-modal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="details-modal-title">Detalles de la Actividad</h3>
            <button type="button" class="modal-close" onclick="closeDetailsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-row">
                <i class="fa-solid fa-tag"></i>
                <div>
                    <strong>Tipo de Actividad</strong>
                    <p id="details-modal-tipo">Cargando...</p>
                </div>
            </div>
            <div class="detail-row">
                <i class="fa-regular fa-calendar-days"></i>
                <div>
                    <strong>Fecha de Ejecución</strong>
                    <p id="details-modal-fecha">Cargando...</p>
                </div>
            </div>
            <div class="detail-row">
                <i class="fa-regular fa-clock"></i>
                <div>
                    <strong>Horario</strong>
                    <p id="details-modal-horario">Cargando...</p>
                </div>
            </div>
            <div class="detail-row">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <strong>Ubicación</strong>
                    <p id="details-modal-ubicacion">Cargando...</p>
                </div>
            </div>
            <div class="detail-row">
                <i class="fa-solid fa-users-viewfinder"></i>
                <div>
                    <strong>Modalidad</strong>
                    <p id="details-modal-modalidad">Cargando...</p>
                </div>
            </div>
            <div class="detail-row">
                <i class="fa-solid fa-align-left"></i>
                <div>
                    <strong>Descripción</strong>
                    <p id="details-modal-descripcion">Cargando...</p>
                </div>
            </div>
        </div>
        <div class="modal-footer" id="details-modal-footer">
            <!-- Botones inyectados dinámicamente por JS -->
        </div>
    </div>
</div>

{{-- 2. MODAL REGISTRO / COMPLEMENTACIÓN DE PERFIL --}}
<div id="quick-profile-modal" class="modal-overlay @if($errors->any() && old('completar_perfil')) active @endif">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Completa tus datos de Inscripción</h3>
            <button type="button" class="modal-close" onclick="closeProfileModal()">&times;</button>
        </div>
        <!-- El atributo 'action' se gestiona de forma segura a través de Javascript -->
        <form id="quick-profile-form" method="POST" action="">
            @csrf
            <div class="modal-body">
                <p style="font-size: var(--text-sm); color: var(--color-text-muted); margin-top:0;">
                    Para inscribirte en <strong id="modal-activity-name"></strong>, requerimos guardar la información obligatoria en tu base de datos:
                </p>
                
                <input type="hidden" name="completar_perfil" value="1">
                
                <div class="form-group">
                    <label for="modal_nombres">Nombres</label>
                    <input type="text" id="modal_nombres" name="nombres" 
                           value="{{ old('nombres', auth()->user()->persona->nombres) }}" 
                           class="js-validate-input @error('nombres') val-red @enderror">
                    @error('nombres') 
                        <span class="error-text" style="color: var(--color-danger-text); font-size: var(--text-xs);">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="form-group">
                    <label for="modal_apellidos">Apellidos</label>
                    <input type="text" id="modal_apellidos" name="apellidos" 
                           value="{{ old('apellidos', auth()->user()->persona->apellidos) }}" 
                           class="js-validate-input @error('apellidos') val-red @enderror">
                    @error('apellidos') 
                        <span class="error-text" style="color: var(--color-danger-text); font-size: var(--text-xs);">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="form-group">
                    <label for="modal_cedula">Cédula de Identidad</label>
                    <input type="text" id="modal_cedula" name="cedula" 
                           value="{{ old('cedula', auth()->user()->persona->cedula) }}" 
                           placeholder="Ej: V-12345678" 
                           class="js-validate-input @error('cedula') val-red @enderror">
                    @error('cedula') 
                        <span class="error-text" style="color: var(--color-danger-text); font-size: var(--text-xs);">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="form-group">
                    <label for="modal_telefono">Teléfono de Contacto</label>
                    <input type="text" id="modal_telefono" name="telefono" 
                           value="{{ old('telefono', auth()->user()->persona->telefono) }}" 
                           placeholder="Ej: +58 412-1234567" 
                           class="js-validate-input @error('telefono') val-red @enderror">
                    @error('telefono') 
                        <span class="error-text" style="color: var(--color-danger-text); font-size: var(--text-xs);">{{ $message }}</span> 
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action btn-cancel" onclick="closeProfileModal()">Cancelar</button>
                <button type="submit" class="btn-action btn-register" style="background-color: var(--color-brand-blue); color: white;">Confirmar Inscripción</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.userProfile = {
            nombres: @json(auth()->user()->persona->nombres),
            apellidos: @json(auth()->user()->persona->apellidos),
            cedula: @json(auth()->user()->persona->cedula),
            telefono: @json(auth()->user()->persona->telefono),
            email: @json(auth()->user()->email)
        };
    </script>
    <script src="{{ asset('assets/js/menu.js') }}"></script>
    <!-- Vinculación del archivo JavaScript separado -->
    <script src="{{ asset('assets/js/publico_general/dashboardPublico.js') }}"></script>
@endpush