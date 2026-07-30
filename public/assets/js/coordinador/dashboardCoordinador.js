document.addEventListener('DOMContentLoaded', () => {
    // Desvanecimiento suave y descarte de alertas de sesión de Laravel tras 4 segundos
    const alertMessages = document.querySelectorAll('.alert-message');
    alertMessages.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('alert-fade-out');
            setTimeout(() => {
                alert.remove();
            }, 600);
        }, 4000);
    });
});

function openCreateModal() {
    const modal = document.getElementById('activity-create-modal');
    if (modal) modal.classList.add('active');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
}

function toggleAccordion(id) {
    const element = document.getElementById(id);
    if (!element) return;
    
    const header = element.previousElementSibling;
    const arrow = header ? header.querySelector('.accordion-arrow') : null;

    if (element.classList.contains('active')) {
        element.classList.remove('active');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
        element.style.maxHeight = '0';
    } else {
        element.classList.add('active');
        if (arrow) arrow.style.transform = 'rotate(180deg)';
        element.style.maxHeight = element.scrollHeight + "px";
    }
}

function openEditModal(id, nombre, descripcion, trimestreId, fecha, fechaInscripcionInicio, fechaInscripcionFin, horaInicio, horaFin, idSalon, idModalidad, idTipoActividad, idTipoDocumento, idTema, estado) {
    const inputId = document.getElementById('edit_id');
    const inputNombre = document.getElementById('edit_nombre');
    const inputDescripcion = document.getElementById('edit_descripcion');
    const selectTrimestre = document.getElementById('edit_trimestre');
    const inputFecha = document.getElementById('edit_fecha');
    const inputInscripcionInicio = document.getElementById('edit_fecha_inscripcion_inicio');
    const inputInscripcionFin = document.getElementById('edit_fecha_inscripcion_fin');
    const inputHoraInicio = document.getElementById('edit_hora_inicio');
    const inputHoraFin = document.getElementById('edit_hora_fin');
    const selectSalon = document.getElementById('edit_id_salon');
    const selectModalidad = document.getElementById('edit_id_modalidad');
    const selectTipoActividad = document.getElementById('edit_id_tipo_actividad');
    const selectTipoDocumento = document.getElementById('edit_id_tipo_documento');
    const selectTema = document.getElementById('edit_id_tema');
    const selectEstado = document.getElementById('edit_estado');
    const form = document.getElementById('edit-activity-form');
    const modal = document.getElementById('activity-edit-modal');

    if (inputId) inputId.value = id;
    if (inputNombre) inputNombre.value = nombre;
    if (inputDescripcion) inputDescripcion.value = descripcion;
    if (selectTrimestre) selectTrimestre.value = trimestreId;
    if (inputFecha) inputFecha.value = fecha;
    if (inputInscripcionInicio) inputInscripcionInicio.value = fechaInscripcionInicio;
    if (inputInscripcionFin) inputInscripcionFin.value = fechaInscripcionFin;
    if (inputHoraInicio) inputHoraInicio.value = horaInicio;
    if (inputHoraFin) inputHoraFin.value = horaFin;
    if (selectSalon) selectSalon.value = idSalon;
    if (selectModalidad) selectModalidad.value = idModalidad;
    if (selectTipoActividad) selectTipoActividad.value = idTipoActividad;
    if (selectTipoDocumento) selectTipoDocumento.value = idTipoDocumento;
    if (selectTema) selectTema.value = idTema;
    if (selectEstado) selectEstado.value = estado;

    if (form) form.action = `/coordinador/actividades/${id}`;
    if (modal) modal.classList.add('active');
}

function openDeleteModal(id) {
    const form = document.getElementById('delete-activity-form');
    const modal = document.getElementById('activity-delete-modal');

    if (form) form.action = `/coordinador/actividades/${id}`;
    if (modal) modal.classList.add('active');
}

function mostrarDetalleActividadDesdeCalendario(data) {
    const modalTitle = document.getElementById('detail-modal-title');
    const modalYear = document.getElementById('detail-modal-year');
    const modalTrimester = document.getElementById('detail-modal-trimester');
    const modalType = document.getElementById('detail-modal-type');
    const modalClassroom = document.getElementById('detail-modal-classroom');
    const modalTime = document.getElementById('detail-modal-time');
    const modalSessions = document.getElementById('detail-modal-sessions');
    const modalContainer = document.getElementById('activity-detail-modal');

    if (modalTitle) modalTitle.innerText = data.nombre || 'N/A';
    
    if (data.type === 'feriado') {
        if (modalYear) modalYear.innerText = 'Día No Laborable / Festivo';
        if (modalTrimester) modalTrimester.innerText = 'Feriado de Calendario';
        if (modalType) modalType.innerText = data.descripcion || 'Feriado Nacional';
        if (modalClassroom) modalClassroom.innerText = 'N/A (Cierre Académico)';
        if (modalTime) modalTime.innerText = data.fecha || 'N/A';
        if (modalSessions) modalSessions.innerText = 'N/A';
    } else {
        if (modalYear) modalYear.innerText = `${data.planificacion_nombre || 'N/A'} (Año: ${data.anio || 'En Curso'})`;
        if (modalTrimester) modalTrimester.innerText = data.trimestre_nombre || 'No asignado';
        if (modalType) modalType.innerText = `${data.modalidad || 'N/A'} - ${data.tipo || 'N/A'}`;
        if (modalClassroom) modalClassroom.innerText = data.aula || 'N/A';
        if (modalTime) {
            modalTime.innerText = `${data.fecha || ''} ${data.horario ? '/ Horario: ' + data.horario : ''}`.trim() || 'N/A';
        }
        
        if (modalSessions) {
            modalSessions.innerHTML = '';
            
            const activityId = data.id_actividad;
            const activityData = (window.ACTIVIDADES_DATA || []).find(act => 
                (activityId && act.id_actividad == activityId) || 
                act.nombre === data.nombre
            );

            if (activityData && activityData.temas && activityData.temas.length > 0) {
                activityData.temas.forEach(tema => {
                    const docName = tema.docente ? (tema.docente.nombre || "Docente Asignado") : "Sin Docente Asignado";
                    const hInicio = formatAMPM(tema.horario_inicio);
                    const hFin = formatAMPM(tema.horario_fin);
                    const desc = tema.descripcion || "Sin descripción disponible.";

                    const subAccordion = document.createElement("div");
                    subAccordion.className = "session-accordion-card";
                    subAccordion.innerHTML = `
                        <button type="button" class="session-accordion-trigger" onclick="toggleSessionDetail('${tema.id_tema}')" style="background-color: #f8fafc; width: 100%; border: none; text-align: left; padding: 0.6rem 0.8rem; font-size: 0.85rem; font-weight: 600; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="color: #1e293b;">Sesión ${tema.numero_de_sesion}: ${tema.tema_sesion}</span>
                            <i class="fa-solid fa-chevron-down" id="arrow-sub-${tema.id_tema}" style="transition: transform 0.2s; color: #64748b;"></i>
                        </button>
                        <div id="session-sub-detail-${tema.id_tema}" class="session-accordion-content" style="display: none; padding: 0.75rem; background-color: #ffffff; font-size: 0.8rem; border-top: 1px solid #e2e8f0; color: #475569; line-height: 1.4;">
                            <p style="margin: 0 0 0.3rem 0;"><strong>Docente:</strong> ${docName}</p>
                            <p style="margin: 0 0 0.3rem 0;"><strong>Hora:</strong> ${hInicio} a ${hFin}</p>
                            <p style="margin: 0 0 0.3rem 0;"><strong>Estado:</strong> <span class="state-indicator-badge state-${tema.estado}" style="text-transform: uppercase; font-size: 0.75rem; font-weight: bold; padding: 0.1rem 0.4rem; border-radius: 4px;">${tema.estado}</span></p>
                            <p style="margin: 0.5rem 0 0 0; padding-top: 0.3rem; border-top: 1px dashed #cbd5e1;"><strong>Descripción:</strong> ${desc}</p>
                        </div>
                    `;
                    modalSessions.appendChild(subAccordion);
                });
            } else {
                modalSessions.innerHTML = `<span style="font-size: 0.9rem; color: #64748b; font-style: italic;"><i class="fa-solid fa-ban"></i> Sin sesiones vinculadas</span>`;
            }
        }
    }
    
    if (modalContainer) modalContainer.classList.add('active');
}

function formatAMPM(timeString) {
    if (!timeString) return "N/A";
    const [hours, minutes] = timeString.split(':');
    let hrs = parseInt(hours, 10);
    const ampm = hrs >= 12 ? 'PM' : 'AM';
    hrs = hrs % 12;
    hrs = hrs ? hrs : 12;
    return `${hrs}:${minutes} ${ampm}`;
}

function toggleSessionDetail(id) {
    const element = document.getElementById(`session-sub-detail-${id}`);
    const arrow = document.getElementById(`arrow-sub-${id}`);
    if (element) {
        const isCollapsed = element.style.display === "none";
        element.style.display = isCollapsed ? "block" : "none";
        if (arrow) {
            arrow.style.transform = isCollapsed ? "rotate(180deg)" : "rotate(0deg)";
        }
    }
}