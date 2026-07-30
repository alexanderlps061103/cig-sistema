document.addEventListener("DOMContentLoaded", function () {
    // Filtrado estricto para remover cualquier tipo de día festivo o feriado
    const academicActivitiesOnly = (window.ACTIVIDADES_DATA || []).filter(item => {
        if (item.type === 'feriado' || item.is_feriado) {
            return false;
        }
        const nameLower = (item.nombre || "").toLowerCase();
        if (nameLower.includes("feriado") || nameLower.includes("natalicio") || nameLower.includes("dia de")) {
            return false;
        }
        return true;
    });

    initDynamicSearch("create_session_activity_search", "create_session_id_actividad", "create_activity_dropdown_results", academicActivitiesOnly, "actividad");
    initDynamicSearch("create_session_teacher_search", "create_session_id_docente", "create_teacher_dropdown_results", window.DOCENTES_DATA || [], "docente");

    initDynamicSearch("edit_session_activity_search", "edit_session_id_actividad", "edit_activity_dropdown_results", academicActivitiesOnly, "actividad");
    initDynamicSearch("edit_session_teacher_search", "edit_session_id_docente", "edit_teacher_dropdown_results", window.DOCENTES_DATA || [], "docente");

    // Asistencia visual de validación de campos del formulario en tiempo real
    setupRealtimeValidation();
});

/**
 * Función genérica de búsqueda dinámica (Comportamiento Flotante)
 */
function initDynamicSearch(inputId, hiddenId, dropdownId, dataArray, type) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);
    const dropdown = document.getElementById(dropdownId);

    if (!input || !dropdown) return;

    input.addEventListener("focus", function () {
        renderDropdownList(dataArray, input, hidden, dropdown, type);
    });

    input.addEventListener("input", function () {
        const query = input.value.toLowerCase().trim();
        const filtered = dataArray.filter(item => {
            let name = "";
            if (type === "actividad") {
                name = item.nombre || "";
            } else {
                name = item.persona 
                    ? `${item.persona.nombres} ${item.persona.apellidos}` 
                    : (item.nombre || item.name || "");
            }
            return name.toLowerCase().includes(query);
        });
        renderDropdownList(filtered, input, hidden, dropdown, type);
    });

    document.addEventListener("click", function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = "none";
        }
    });
}

function renderDropdownList(list, input, hidden, dropdown, type) {
    dropdown.innerHTML = "";
    if (list.length === 0) {
        dropdown.innerHTML = `<div class="dropdown-no-results">Sin coincidencias</div>`;
        dropdown.style.display = "block";
        return;
    }

    list.forEach(item => {
        // CORRECCIÓN: Resuelve prioritariamente 'item.id' para la actividad mapeada
        const id = type === "actividad" ? (item.id || item.id_actividad) : (item.id || item.id_docente);
        
        let name = "";
        if (type === "actividad") {
            name = item.nombre;
        } else {
            name = item.persona 
                ? `${item.persona.nombres} ${item.persona.apellidos}` 
                : (item.nombre || item.name || "Sin Nombre");
        }
        
        const option = document.createElement("div");
        option.className = "dropdown-item-option";
        option.textContent = name;
        
        option.addEventListener("click", function () {
            input.value = name;
            hidden.value = id;
            dropdown.style.display = "none";
            
            // Efecto feedback visual inmediato tras selección válida
            input.classList.remove('val-red', 'val-yellow');
            input.classList.add('val-green');
            
            if (type === "actividad") {
                const dateContainer = input.closest('form').querySelector('.activity-date-display');
                if (dateContainer && item.fecha) {
                    const parsedDate = item.fecha.includes('-') ? item.fecha : new Date(item.fecha);
                    const formattedDate = new Date(parsedDate).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                    dateContainer.innerHTML = `<i class="fa-solid fa-calendar-day"></i> Fecha de ejecución: <strong>${formattedDate}</strong>`;
                    dateContainer.style.display = "block";
                }
            }
        });
        dropdown.appendChild(option);
    });

    dropdown.style.display = "block";
}

/**
 * Módulo de Asistencia Visual de Validación (Coloreado de Bordes)
 */
function setupRealtimeValidation() {
    const inputsToValidate = document.querySelectorAll('input[type="text"], textarea');
    const validRegex = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\-#]*$/;

    inputsToValidate.forEach(input => {
        input.addEventListener('input', () => {
            const value = input.value.trim();
            input.classList.remove('val-red', 'val-yellow', 'val-green');

            if (value === '' || !validRegex.test(value)) {
                input.classList.add('val-red');
            } else if (value.length < 3) {
                input.classList.add('val-yellow');
            } else {
                input.classList.add('val-green');
            }
        });
    });
}

function openCreateSessionModal() {
    const modal = document.getElementById("session-create-modal");
    if (modal) {
        modal.classList.add("active");
    }
}

function openEditSessionModal(id, idActividad, nombreActividad, idDocente, nombreDocente, temaSesion, descripcion, numero, horaInicio, horaFin, estado) {
    const modal = document.getElementById("session-edit-modal");
    if (!modal) return;

    const form = document.getElementById("edit-session-form");
    if (form) {
        form.action = `/coordinador/sesiones/${id}`;
    }

    document.getElementById("edit_session_id").value = id;
    document.getElementById("edit_session_id_actividad").value = idActividad;
    document.getElementById("edit_session_activity_search").value = nombreActividad;
    document.getElementById("edit_session_id_docente").value = idDocente;
    document.getElementById("edit_session_teacher_search").value = nombreDocente;
    document.getElementById("edit_session_tema_sesion").value = temaSesion;
    document.getElementById("edit_session_descripcion").value = descripcion || "";
    document.getElementById("edit_session_numero_de_sesion").value = numero;
    document.getElementById("edit_session_hora_inicio").value = horaInicio;
    document.getElementById("edit_session_hora_fin").value = horaFin;
    document.getElementById("edit_session_estado").value = estado;

    modal.classList.add("active");
}

function openDeleteSessionModal(id) {
    const modal = document.getElementById("session-delete-modal");
    if (!modal) return;
    const form = document.getElementById("delete-session-form");
    if (form) {
        form.action = `/coordinador/sesiones/${id}`;
    }
    modal.classList.add("active");
}

function closeSessionModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove("active");
    }
}