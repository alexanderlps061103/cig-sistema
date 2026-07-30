@extends('layouts.app')

@section('title', 'Panel de Coordinación')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/dashboardCoordinador.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/calendario.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/actividadesListado.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coordinador/sesion.css') }}">
@endpush

@section('content')
<div class="dashboard-scroll-container">

    <header class="main-header">
        <div class="header-welcome">
            <h1>Panel de Coordinación</h1>
            <p>Gestión operativa y académica del sistema.</p>
        </div>
        <div class="header-date">
            <i class="fa-regular fa-calendar"></i>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('d F, Y') }}</span>
        </div>
    </header>

    {{-- KPIs --}}
    <section class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon icon-blue"><i class="fa-solid fa-calendar-day"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Actividades</span>
                <span class="kpi-value">{{ $counts['actividades'] ?? 0 }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-green"><i class="fa-solid fa-clock"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Sesiones</span>
                <span class="kpi-value">{{ $counts['sesiones'] ?? 0 }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-purple"><i class="fa-solid fa-school"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Espacios Activos</span>
                <span class="kpi-value">{{ $counts['espacios'] ?? 0 }}</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon icon-orange"><i class="fa-solid fa-user-clock"></i></div>
            <div class="kpi-info">
                <span class="kpi-title">Inscripciones</span>
                <span class="kpi-value">{{ $counts['inscripciones_pendientes'] ?? 0 }}</span>
            </div>
        </div>
    </section>

    <!-- Alertas Flotantes -->
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

    <div class="action-header-bar">
        <button class="btn-create-activity" onclick="openCreateModal()">
            <i class="fa-solid fa-plus"></i> Crear Actividad
        </button>
        <button class="btn-create-session" onclick="openCreateSessionModal()">
            <i class="fa-solid fa-clock"></i> Crear Sesión
        </button>
    </div>

    <section class="calendar-section-wrapper">
        @include('coordinador.calendario')
    </section>

    <section class="activities-list-section-wrapper">
        @include('coordinador.actividadesListado')
    </section>

</div>

{{-- ==================== MODALES ==================== --}}

{{-- 1. MODAL DETALLES --}}
<div id="activity-detail-modal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="detail-modal-title">Detalle de la Actividad</h3>
            <button class="modal-close" onclick="closeModal('activity-detail-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-detail-row">
                <i class="fa-solid fa-bookmark"></i>
                <div><strong>Planificación / Año</strong><p id="detail-modal-year">Cargando...</p></div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-calendar-week"></i>
                <div><strong>Trimestre</strong><p id="detail-modal-trimester">Cargando...</p></div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-circle-info"></i>
                <div><strong>Modalidad y Tipo</strong><p id="detail-modal-type">Cargando...</p></div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-location-dot"></i>
                <div><strong>Salón / Aula</strong><p id="detail-modal-classroom">Cargando...</p></div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-clock"></i>
                <div><strong>Fecha y Horario</strong><p id="detail-modal-time">Cargando...</p></div>
            </div>
            <div class="modal-detail-row">
                <i class="fa-solid fa-users"></i>
                <div>
                    <strong>Sesiones Vinculadas</strong>
                    <div id="detail-modal-sessions-container" style="margin-top: 0.5rem;">
                        <p id="detail-modal-sessions">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. MODAL CREAR ACTIVIDAD (Se activa únicamente si existen errores específicos de actividad) --}}
<div id="activity-create-modal" class="modal-overlay @if($errors->hasAny(['nombre', 'fecha_inscripcion_inicio', 'fecha_inscripcion_fin', 'fecha', 'id_salon', 'id_modalidad', 'id_trimestre']) && !old('_method')) active @endif">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Registrar Nueva Actividad</h3>
            <button class="modal-close" onclick="closeModal('activity-create-modal')">&times;</button>
        </div>
        <form action="{{ route('coordinador.actividades.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="create_nombre">Nombre de la Actividad</label>
                    <input type="text" id="create_nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Taller de Redes" class="@error('nombre') val-red @enderror">
                    @error('nombre') <span class="error-input-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="create_descripcion">Descripción</label>
                    <textarea id="create_descripcion" name="descripcion" placeholder="Describa brevemente la actividad..." class="@error('descripcion') val-red @enderror">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <span class="error-input-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="create_id_trimestre">Trimestre Académico</label>
                        <select id="create_id_trimestre" name="id_trimestre" class="@error('id_trimestre') val-red @enderror">
                            <option value="">Seleccione un Trimestre</option>
                            @foreach($trimestres ?? [] as $t)
                                <option value="{{ $t->id_trimestre }}" {{ old('id_trimestre') == $t->id_trimestre ? 'selected' : '' }}>{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_trimestre') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="create_estado">Estado</label>
                        <select id="create_estado" name="estado" class="@error('estado') val-red @enderror">
                            <option value="">Seleccione el estado</option>
                            <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="activa" {{ old('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="culminada" {{ old('estado') == 'culminada' ? 'selected' : '' }}>Culminada</option>
                        </select>
                        @error('estado') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="create_fecha_inscripcion_inicio">Inscripción - Inicio</label>
                        <input type="date" id="create_fecha_inscripcion_inicio" name="fecha_inscripcion_inicio" value="{{ old('fecha_inscripcion_inicio') }}" class="@error('fecha_inscripcion_inicio') val-red @enderror">
                        @error('fecha_inscripcion_inicio') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="create_fecha_inscripcion_fin">Inscripción - Fin</label>
                        <input type="date" id="create_fecha_inscripcion_fin" name="fecha_inscripcion_fin" value="{{ old('fecha_inscripcion_fin') }}" class="@error('fecha_inscripcion_fin') val-red @enderror">
                        @error('fecha_inscripcion_fin') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="create_fecha">Fecha de Ejecución</label>
                        <input type="date" id="create_fecha" name="fecha" value="{{ old('fecha') }}" class="@error('fecha') val-red @enderror">
                        @error('fecha') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="create_id_salon">Salón / Aula</label>
                        <select id="create_id_salon" name="id_salon" class="@error('id_salon') val-red @enderror">
                            <option value="">Seleccione un Salón</option>
                            @foreach($salones ?? [] as $salon)
                                <option value="{{ $salon->id_salon }}" {{ old('id_salon') == $salon->id_salon ? 'selected' : '' }}>{{ $salon->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_salon') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="create_hora_inicio">Hora de Inicio</label>
                        <input type="time" id="create_hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') }}" class="@error('hora_inicio') val-red @enderror">
                        @error('hora_inicio') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="create_hora_fin">Hora de Finalización</label>
                        <input type="time" id="create_hora_fin" name="hora_fin" value="{{ old('hora_fin') }}" class="@error('hora_fin') val-red @enderror">
                        @error('hora_fin') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="create_id_modalidad">Modalidad</label>
                        <select id="create_id_modalidad" name="id_modalidad" class="@error('id_modalidad') val-red @enderror">
                            <option value="">Seleccione Modalidad</option>
                            @foreach($modalidades ?? [] as $mod)
                                <option value="{{ $mod->id_modalidad }}" {{ old('id_modalidad') == $mod->id_modalidad ? 'selected' : '' }}>{{ $mod->nombre ?? $mod->nombre_modalidad ?? 'Sin Nombre' }}</option>
                            @endforeach
                        </select>
                        @error('id_modalidad') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="create_id_tipo_actividad">Tipo de Actividad</label>
                        <select id="create_id_tipo_actividad" name="id_tipo_actividad" class="@error('id_tipo_actividad') val-red @enderror">
                            <option value="">Seleccione Tipo</option>
                            @foreach($tipoActividades ?? [] as $tipo)
                                <option value="{{ $tipo->id_tipo_actividad }}" {{ old('id_tipo_actividad') == $tipo->id_tipo_actividad ? 'selected' : '' }}>{{ $tipo->nombre ?? 'Sin Nombre' }}</option>
                            @endforeach
                        </select>
                        @error('id_tipo_actividad') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Organizador Opcional --}}
                <div class="form-group">
                    <label for="create_id_organizador">Organizador (Opcional)</label>
                    <select id="create_id_organizador" name="id_organizador" class="@error('id_organizador') val-red @enderror">
                        <option value="">Seleccione un Organizador</option>
                        @foreach($docentes ?? [] as $docente)
                            @if($docente->persona)
                                <option value="{{ $docente->persona->id }}" {{ old('id_organizador') == $docente->persona->id ? 'selected' : '' }}>
                                    {{ $docente->persona->nombres }} {{ $docente->persona->apellidos }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('id_organizador') <span class="error-input-text">{{ $message }}</span> @enderror
                </div>

                <input type="hidden" name="id_tipo_documento" value="{{ $tipoDocumentos->first()->id_tipo_documento ?? 1 }}">
                <input type="hidden" name="id_tema" value="{{ $temas->first()->id_tema ?? 1 }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('activity-create-modal')">Cancelar</button>
                <button type="submit" class="btn-submit">Guardar Actividad</button>
            </div>
        </form>
    </div>
</div>

{{-- 3. MODAL EDITAR ACTIVIDAD --}}
<div id="activity-edit-modal" class="modal-overlay @if($errors->any() && old('_method') === 'PUT') active @endif">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Actualizar Configuración de Actividad</h3>
            <button class="modal-close" onclick="closeModal('activity-edit-modal')">&times;</button>
        </div>
        <form id="edit-activity-form" action="{{ old('edit_id') ? route('coordinador.actividades.update', ['actividade' => old('edit_id')]) : '' }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <input type="hidden" id="edit_id" name="edit_id" value="{{ old('edit_id') }}">
                
                <div class="form-group">
                    <label for="edit_nombre">Nombre de la Actividad</label>
                    <input type="text" id="edit_nombre" name="nombre" value="{{ old('nombre') }}" class="@error('nombre') val-red @enderror">
                    @error('nombre') <span class="error-input-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="edit_descripcion">Descripción</label>
                    <textarea id="edit_descripcion" name="descripcion" class="@error('descripcion') val-red @enderror">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <span class="error-input-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_trimestre">Trimestre Académico</label>
                        <select id="edit_trimestre" name="id_trimestre" class="@error('id_trimestre') val-red @enderror">
                            @foreach($trimestres ?? [] as $t)
                                <option value="{{ $t->id_trimestre }}" {{ old('id_trimestre') == $t->id_trimestre ? 'selected' : '' }}>{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_trimestre') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="edit_estado">Estado</label>
                        <select id="edit_estado" name="estado" class="@error('estado') val-red @enderror">
                            <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="activa" {{ old('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="culminada" {{ old('estado') == 'culminada' ? 'selected' : '' }}>Culminada</option>
                        </select>
                        @error('estado') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_fecha_inscripcion_inicio">Inscripción - Inicio</label>
                        <input type="date" id="edit_fecha_inscripcion_inicio" name="fecha_inscripcion_inicio" value="{{ old('fecha_inscripcion_inicio') }}" class="@error('fecha_inscripcion_inicio') val-red @enderror">
                        @error('fecha_inscripcion_inicio') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="edit_fecha_inscripcion_fin">Inscripción - Fin</label>
                        <input type="date" id="edit_fecha_inscripcion_fin" name="fecha_inscripcion_fin" value="{{ old('fecha_inscripcion_fin') }}" class="@error('fecha_inscripcion_fin') val-red @enderror">
                        @error('fecha_inscripcion_fin') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_fecha">Fecha de Ejecución</label>
                        <input type="date" id="edit_fecha" name="fecha" value="{{ old('fecha') }}" class="@error('fecha') val-red @enderror">
                        @error('fecha') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="edit_id_salon">Salón / Aula</label>
                        <select id="edit_id_salon" name="id_salon" class="@error('id_salon') val-red @enderror">
                            @foreach($salones ?? [] as $salon)
                                <option value="{{ $salon->id_salon }}" {{ old('id_salon') == $salon->id_salon ? 'selected' : '' }}>{{ $salon->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_salon') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_hora_inicio">Hora de Inicio</label>
                        <input type="time" id="edit_hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') }}" class="@error('hora_inicio') val-red @enderror">
                        @error('hora_inicio') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="edit_hora_fin">Hora de Finalización</label>
                        <input type="time" id="edit_hora_fin" name="hora_fin" value="{{ old('hora_fin') }}" class="@error('hora_fin') val-red @enderror">
                        @error('hora_fin') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_id_modalidad">Modalidad</label>
                        <select id="edit_id_modalidad" name="id_modalidad" class="@error('id_modalidad') val-red @enderror">
                            @foreach($modalidades ?? [] as $mod)
                                <option value="{{ $mod->id_modalidad }}" {{ old('id_modalidad') == $mod->id_modalidad ? 'selected' : '' }}>{{ $mod->nombre ?? $mod->nombre_modalidad ?? 'Sin Nombre' }}</option>
                            @endforeach
                        </select>
                        @error('id_modalidad') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="edit_id_tipo_actividad">Tipo de Actividad</label>
                        <select id="edit_id_tipo_actividad" name="id_tipo_actividad" class="@error('id_tipo_actividad') val-red @enderror">
                            @foreach($tipoActividades ?? [] as $tipo)
                                <option value="{{ $tipo->id_tipo_actividad }}" {{ old('id_tipo_actividad') == $tipo->id_tipo_actividad ? 'selected' : '' }}>{{ $tipo->nombre ?? 'Sin Nombre' }}</option>
                            @endforeach
                        </select>
                        @error('id_tipo_actividad') <span class="error-input-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Organizador Opcional --}}
                <div class="form-group">
                    <label for="edit_id_organizador">Organizador (Opcional)</label>
                    <select id="edit_id_organizador" name="id_organizador" class="@error('edit_id_organizador') val-red @enderror">
                        <option value="">Seleccione un Organizador</option>
                        @foreach($docentes ?? [] as $docente)
                            @if($docente->persona)
                                <option value="{{ $docente->persona->id }}" {{ old('id_organizador') == $docente->persona->id ? 'selected' : '' }}>
                                    {{ $docente->persona->nombres }} {{ $docente->persona->apellidos }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('id_organizador') <span class="error-input-text">{{ $message }}</span> @enderror
                </div>

                <input type="hidden" id="edit_id_tipo_documento" name="id_tipo_documento" value="{{ $tipoDocumentos->first()->id_tipo_documento ?? 1 }}">
                <input type="hidden" id="edit_id_tema" name="id_tema" value="{{ $temas->first()->id_tema ?? 1 }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('activity-edit-modal')">Cancelar</button>
                <button type="submit" class="btn-submit-save">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- 4. MODAL ELIMINAR ACTIVIDAD --}}
<div id="activity-delete-modal" class="modal-overlay">
    <div class="modal-container modal-alert">
        <div class="modal-alert-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
        <h3>¿Está seguro de que desea eliminar esta actividad?</h3>
        <p>Esta acción no se puede deshacer y desvinculará todas las sesiones asociadas.</p>
        <form id="delete-activity-form" action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-footer justify-center">
                <button type="button" class="btn-cancel" onclick="closeModal('activity-delete-modal')">Cancelar</button>
                <button type="submit" class="btn-danger-submit">Eliminar</button>
            </div>
        </form>
    </div>
</div>


{{-- ==================== NUEVOS MODALES DE GESTIÓN DE SESIONES ==================== --}}

{{-- A. NUEVO: MODAL CREAR SESIÓN (Se activa únicamente si existen errores específicos de sesión) --}}
<div id="session-create-modal" class="modal-overlay @if($errors->hasAny(['id_actividad', 'id_docente', 'tema_sesion', 'numero_de_sesion', 'horario_inicio', 'horario_fin'])) active @endif">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Registrar Nueva Sesión (Tema)</h3>
            <button class="modal-close" onclick="closeSessionModal('session-create-modal')">&times;</button>
        </div>
        <form action="{{ route('coordinador.sesiones.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                
                {{-- Buscador Dinámico de Actividad --}}
                <div class="form-group custom-select-wrapper">
                    <label for="create_session_activity_search">Actividad Asociada</label>
                    <div class="custom-select-container">
                        <input type="text" id="create_session_activity_search" placeholder="Escriba para buscar actividad..." autocomplete="off" class="custom-select-search-input">
                        <input type="hidden" id="create_session_id_actividad" name="id_actividad">
                        <div class="custom-select-dropdown" id="create_activity_dropdown_results" style="display: none;"></div>
                    </div>
                    <div class="activity-date-display" style="display: none;"></div>
                </div>

                {{-- Buscador Dinámico de Docente --}}
                <div class="form-group custom-select-wrapper">
                    <label for="create_session_teacher_search">Docente Responsable</label>
                    <div class="custom-select-container">
                        <input type="text" id="create_session_teacher_search" placeholder="Escriba para buscar docente..." autocomplete="off" class="custom-select-search-input">
                        <input type="hidden" id="create_session_id_docente" name="id_docente">
                        <div class="custom-select-dropdown" id="create_teacher_dropdown_results" style="display: none;"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="create_session_tema_sesion">Título del Tema / Sesión</label>
                        <input type="text" id="create_session_tema_sesion" name="tema_sesion" placeholder="Ej: Fundamentos de Ciberseguridad">
                    </div>
                    <div class="form-group">
                        <label for="create_session_numero_de_sesion">Número de Sesión</label>
                        <input type="number" id="create_session_numero_de_sesion" name="numero_de_sesion" placeholder="Ej: 1" min="1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="create_session_descripcion">Descripción de la Sesión</label>
                    <textarea id="create_session_descripcion" name="descripcion" placeholder="Describa el contenido temático..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="create_session_hora_inicio">Hora Inicio</label>
                        <input type="time" id="create_session_hora_inicio" name="horario_inicio">
                    </div>
                    <div class="form-group">
                        <label for="create_session_hora_fin">Hora Fin</label>
                        <input type="time" id="create_session_hora_fin" name="horario_fin">
                    </div>
                    <div class="form-group">
                        <label for="create_session_estado">Estado</label>
                        <select id="create_session_estado" name="estado">
                            <option value="espera">En espera</option>
                            <option value="curso">En curso</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeSessionModal('session-create-modal')">Cancelar</button>
                <button type="submit" class="btn-submit" style="background-color: #0d9488;">Vincular Sesión</button>
            </div>
        </form>
    </div>
</div>

{{-- B. NUEVO: MODAL EDITAR SESIÓN --}}
<div id="session-edit-modal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Actualizar Sesión (Tema)</h3>
            <button class="modal-close" onclick="closeSessionModal('session-edit-modal')">&times;</button>
        </div>
        <form id="edit-session-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <input type="hidden" id="edit_session_id" name="edit_id">

                {{-- Buscador Dinámico de Actividad --}}
                <div class="form-group custom-select-wrapper">
                    <label for="edit_session_activity_search">Actividad Asociada</label>
                    <div class="custom-select-container">
                        <input type="text" id="edit_session_activity_search" autocomplete="off" class="custom-select-search-input">
                        <input type="hidden" id="edit_session_id_actividad" name="id_actividad">
                        <div class="custom-select-dropdown" id="edit_activity_dropdown_results" style="display: none;"></div>
                    </div>
                </div>

                {{-- Buscador Dinámico de Docente --}}
                <div class="form-group custom-select-wrapper">
                    <label for="edit_session_teacher_search">Docente Responsable</label>
                    <div class="custom-select-container">
                        <input type="text" id="edit_session_teacher_search" autocomplete="off" class="custom-select-search-input">
                        <input type="hidden" id="edit_session_id_docente" name="id_docente">
                        <div class="custom-select-dropdown" id="edit_teacher_dropdown_results" style="display: none;"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_session_tema_sesion">Título del Tema / Sesión</label>
                        <input type="text" id="edit_session_tema_sesion" name="tema_sesion">
                    </div>
                    <div class="form-group">
                        <label for="edit_session_numero_de_sesion">Número de Sesión</label>
                        <input type="number" id="edit_session_numero_de_sesion" name="numero_de_sesion" min="1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_session_descripcion">Descripción de la Sesión</label>
                    <textarea id="edit_session_descripcion" name="descripcion"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_session_hora_inicio">Hora Inicio</label>
                        <input type="time" id="edit_session_hora_inicio" name="horario_inicio">
                    </div>
                    <div class="form-group">
                        <label for="edit_session_hora_fin">Hora Fin</label>
                        <input type="time" id="edit_session_hora_fin" name="horario_fin">
                    </div>
                    <div class="form-group">
                        <label for="edit_session_estado">Estado</label>
                        <select id="edit_session_estado" name="estado">
                            <option value="espera">En espera</option>
                            <option value="curso">En curso</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeSessionModal('session-edit-modal')">Cancelar</button>
                <button type="submit" class="btn-submit-save" style="background-color: #0d9488;">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- C. NUEVO: MODAL ELIMINAR SESIÓN --}}
<div id="session-delete-modal" class="modal-overlay">
    <div class="modal-container modal-alert">
        <div class="modal-alert-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
        <h3>¿Está seguro de que desea eliminar esta sesión?</h3>
        <p>Esta acción desvinculará al docente asignado del bloque temático seleccionado.</p>
        <form id="delete-session-form" action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-footer justify-center">
                <button type="button" class="btn-cancel" onclick="closeSessionModal('session-delete-modal')">Cancelar</button>
                <button type="submit" class="btn-danger-submit">Eliminar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        window.ACTIVIDADES_DATA = @json($actividadesMapped ?? []);
        window.DOCENTES_DATA = @json($docentes ?? []);
    </script>
    <script src="{{ asset('assets/js/coordinador/dashboardCoordinador.js') }}"></script>
    <script src="{{ asset('assets/js/coordinador/calendario.js') }}"></script>
    <script src="{{ asset('assets/js/coordinador/actividadesListado.js') }}"></script>
    <script src="{{ asset('assets/js/coordinador/actividadesSesiones.js') }}"></script>
@endpush