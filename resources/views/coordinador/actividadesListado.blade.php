@push('styles')
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush
<div class="trimester-accordion-container">
    <div class="accordion-header-filters">
        <h3 class="listado-section-title">Planificación Académica por Trimestres</h3>
        
        <div class="list-filters-wrapper">
            <div class="search-input-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="filter-search" placeholder="Buscar actividad..." autocomplete="off">
            </div>
            <select id="filter-status">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="activa">Activa</option>
                <option value="culminada">Culminada</option>
            </select>
        </div>
    </div>

    @forelse($trimestres ?? [] as $trimestre)
        <div class="accordion-item" data-trimestre-id="{{ $trimestre->id_trimestre }}">
            
            <button class="accordion-header" onclick="toggleAccordion('trimestre-{{ $trimestre->id_trimestre }}')">
                <span class="trimestre-info">
                    <strong>{{ $trimestre->nombre }}</strong> 
                    <span class="dates-range">
                        (Desde: {{ \Carbon\Carbon::parse($trimestre->fecha_inicio)->format('d/m/Y') }} hasta: {{ \Carbon\Carbon::parse($trimestre->fecha_fin)->format('d/m/Y') }})
                    </span>
                </span>
                <i class="fa-solid fa-chevron-down accordion-arrow"></i>
            </button>

            <div id="trimestre-{{ $trimestre->id_trimestre }}" class="accordion-content">
                
                {{-- Cabecera estructurada tipo tabla --}}
                <div class="activity-table-header-row">
                    <div class="col-header-info">Actividad / Información</div>
                    <div class="col-header-activity-actions">Gestión de Actividades</div>
                    <div class="col-header-session-actions">Gestión de Sesiones (Temas)</div>
                </div>

                <ul class="activities-list-ul">
                    @forelse($trimestre->actividades ?? [] as $actividad)
                        <li class="activity-row-item data-row" 
                            data-nombre="{{ strtolower($actividad->nombre) }}" 
                            data-descripcion="{{ strtolower($actividad->descripcion) }}"
                            data-estado="{{ strtolower($actividad->estado) }}">
                            
                            {{-- Columna 1: Información de Actividad --}}
                            <div class="activity-info-col">
                                <span class="activity-item-name" style="font-weight: 600;" 
                                    onclick="mostrarDetalleActividadDesdeCalendario({
                                        id_actividad: '{{ $actividad->id_actividad }}',
                                        nombre: '{{ addslashes($actividad->nombre) }}',
                                        type: 'actividad',
                                        planificacion_nombre: '{{ addslashes($trimestre->planificacion->titulo ?? 'N/A') }}',
                                        anio: '{{ $trimestre->planificacion->anio ?? 'En Curso' }}',
                                        trimestre_nombre: '{{ addslashes($trimestre->nombre) }}',
                                        modalidad: '{{ $actividad->modalidadRelacion->nombre ?? 'Presencial' }}',
                                        tipo: '{{ addslashes($actividad->tipo->nombre ?? 'N/A') }}',
                                        aula: '{{ addslashes($actividad->salon->nombre ?? 'N/A') }}',
                                        fecha: '{{ $actividad->fecha->format('d/m/Y') }}',
                                        horario: '{{ \Carbon\Carbon::parse($actividad->hora_inicio)->format('g:i A') }} a {{ \Carbon\Carbon::parse($actividad->hora_fin)->format('g:i A') }}',
                                        sesiones_conteo: {{ $actividad->temas->count() }}
                                    })">
                                    <i class="fa-solid fa-circle-play icon-bullet"></i> {{ $actividad->nombre }}
                                    <span class="state-indicator-badge state-{{ $actividad->estado }}">{{ ucfirst($actividad->estado) }}</span>
                                </span>
                                <p class="activity-row-description-meta">
                                    {{ \Illuminate\Support\Str::limit($actividad->descripcion, 80) }}
                                </p>
                            </div>
                            
                            {{-- Columna 2: Gestión de Actividad (Editar / Inhabilitar) --}}
                            <div class="activity-actions-col">
                                <button class="action-icon-btn edit-btn" 
                                    onclick="openEditModal(
                                        '{{ $actividad->id_actividad }}', 
                                        '{{ addslashes($actividad->nombre) }}', 
                                        '{{ $actividad->descripcion ? addslashes($actividad->descripcion) : '' }}',
                                        '{{ $trimestre->id_trimestre }}', 
                                        '{{ $actividad->fecha->format('Y-m-d') }}', 
                                        '{{ $actividad->fecha_inscripcion_inicio->format('Y-m-d') }}', 
                                        '{{ $actividad->fecha_inscripcion_fin->format('Y-m-d') }}', 
                                        '{{ $actividad->hora_inicio }}', 
                                        '{{ $actividad->hora_fin }}', 
                                        '{{ $actividad->id_salon }}', 
                                        '{{ $actividad->id_modalidad }}', 
                                        '{{ $actividad->id_tipo_actividad }}',
                                        '{{ $actividad->id_tipo_documento }}',
                                        '{{ $actividad->id_tema }}',
                                        '{{ $actividad->estado }}'
                                    )" 
                                    title="Modificar Actividad">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="action-icon-btn delete-btn" 
                                    onclick="openDeleteModal('{{ $actividad->id_actividad }}')" 
                                    title="Inhabilitar Actividad">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </div>

                            {{-- Columna 3: Gestión y Acciones individuales por Sesión (Nombre, Docente, Horario, Modificar, Inhabilitar) --}}
                            <div class="session-actions-col">
                                @forelse($actividad->temas ?? [] as $tema)
                                    <div class="session-action-row">
                                        <div class="session-info-block">
                                            <span class="session-row-title" title="{{ $tema->tema_sesion }}">
                                                Sesión {{ $tema->numero_de_sesion }}: {{ \Illuminate\Support\Str::limit($tema->tema_sesion, 24) }}
                                            </span>
                                            <span class="session-row-meta">
                                                {{-- CORRECCIÓN: Llamamos al accessor $tema->docente directamente --}}
                                                <strong>Docente:</strong> {{ $tema->docente }} <br>
                                                <strong>Horario:</strong> {{ \Carbon\Carbon::parse($tema->horario_inicio)->format('g:i A') }} - {{ \Carbon\Carbon::parse($tema->horario_fin)->format('g:i A') }}
                                            </span>
                                        </div>
                                        <div class="session-action-buttons">
                                            <span class="session-badge-status session-status-{{ $tema->estado }}">{{ ucfirst($tema->estado) }}</span>
                                            <button class="action-icon-btn edit-session-btn" 
                                                onclick="event.stopPropagation(); openEditSessionModal(
                                                    '{{ $tema->id_tema }}',
                                                    '{{ $actividad->id_actividad }}',
                                                    '{{ addslashes($actividad->nombre) }}',
                                                    '{{ $tema->id_docente }}',
                                                    {{-- CORRECCIÓN: Evitamos leer ->nombre sobre la cadena, pasamos el accessor directo --}}
                                                    '{{ $tema->docente ? addslashes($tema->docente) : '' }}',
                                                    '{{ addslashes($tema->tema_sesion) }}',
                                                    '{{ $tema->descripcion ? addslashes($tema->descripcion) : '' }}',
                                                    '{{ $tema->numero_de_sesion }}',
                                                    '{{ $tema->horario_inicio }}',
                                                    '{{ $tema->horario_fin }}',
                                                    '{{ $tema->estado }}'
                                                )" 
                                                title="Modificar Sesión">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button class="action-icon-btn delete-session-btn" 
                                                onclick="event.stopPropagation(); openDeleteSessionModal('{{ $tema->id_tema }}')" 
                                                title="Inhabilitar Sesión">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="no-sessions-placeholder">
                                        <i class="fa-solid fa-ban"></i> Sin sesiones asignadas
                                    </div>
                                @endforelse
                            </div>

                        </li>
                    @empty
                        <li class="activity-row-empty">No hay actividades planificadas para este trimestre.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @empty
        <div class="accordion-empty-state">
            <i class="fa-solid fa-calendar-minus"></i>
            <p>No se encontraron trimestres configurados para este periodo.</p>
        </div>
    @endforelse
    
    <div class="accordion-empty-state" id="no-results-row" style="display: none;">
        <i class="fa-solid fa-magnifying-glass"></i>
        <p>No se encontraron actividades que coincidan con la búsqueda.</p>
    </div>
</div>