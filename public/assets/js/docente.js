document.addEventListener('DOMContentLoaded', () => {
    // === ELEMENTOS DEL MODAL DE DETALLES Y CONTROL ===
    const modal = document.getElementById('activity-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const modalTitle = document.getElementById('modal-title');
    const modalTime = document.getElementById('modal-time');
    const modalLocation = document.getElementById('modal-location');
    const modalCapacity = document.getElementById('modal-capacity');

    // Botones de acción del modal (Métodos UML del Docente)
    const btnAttendance = document.getElementById('btn-attendance');
    const btnComplete = document.getElementById('btn-complete');

    // === BASE DE DATOS LOCAL DE PRUEBA (MOCKUP DATA DE SESIONES) ===
    const mockSessions = {
        "1": {
            titulo: "Sesión 01: Enrutamiento Estático y Dinámico",
            horario: "Viernes 15 de Noviembre - 10:00 AM a 12:00 PM",
            salon: "Laboratorio de Redes (Planta Alta)",
            capacidad: "22 Presentes / 30 Alumnos Inscritos"
        },
        "2": {
            titulo: "Sesión 02: Configuración de VLANs y Seguridad",
            horario: "Viernes 22 de Noviembre - 10:00 AM a 12:00 PM",
            salon: "Laboratorio de Redes (Planta Alta)",
            capacidad: "0 Presentes / 30 Alumnos Inscritos (Pendiente)"
        },
        "3": {
            titulo: "Sesión 01: Liderazgo y Gestión de Equipos",
            horario: "Miércoles 20 de Noviembre - 02:00 PM a 04:00 PM",
            salon: "Auditorio Principal (Planta Baja)",
            capacidad: "74 Presentes / 80 Alumnos Inscritos"
        }
    };

    // === GESTIÓN DE DETALLES Y APERTURA DE MODAL ===
    const manageSessionBtns = document.querySelectorAll('.manage-session-btn');

    manageSessionBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation(); // Evita que se propaguen eventos no deseados
            const sessionId = btn.getAttribute('data-id');
            const data = mockSessions[sessionId];

            if (data) {
                // Inyectamos los datos en el modal utilizando guardas de seguridad
                if (modalTitle) modalTitle.textContent = data.titulo;
                if (modalTime) modalTime.textContent = data.horario;
                if (modalLocation) modalLocation.textContent = data.salon;
                if (modalCapacity) modalCapacity.textContent = data.capacidad;

                // Mostramos el modal
                if (modal) modal.classList.add('active');
            }
        });
    });

    // === ACCIONES DE SIMULACIÓN DE MÉTODOS (UML) ===

    // Método: registrarAsistenciaSesion()
    if (btnAttendance) {
        btnAttendance.addEventListener('click', () => {
            alert("Iniciando escáner de cámara para validación de asistencia por código QR...");
            // Aquí se conectaría la lógica del lector de código QR
        });
    }

    // Método: marcarSesionCompletada()
    if (btnComplete) {
        btnComplete.addEventListener('click', () => {
            const confirmacion = confirm("¿Está seguro de que desea marcar esta sesión como Completada?");
            if (confirmacion && modal) {
                alert("Sesión guardada y marcada como 'Completada' de forma exitosa.");
                modal.classList.remove('active');
                // Aquí se actualizaría el estado de la sesión en el backend
            }
        });
    }

    // === FUNCIONES DE CIERRE DEL MODAL ===
    if (closeModalBtn && modal) {
        closeModalBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    // Cerrar al hacer clic fuera del contenedor del modal
    window.addEventListener('click', (e) => {
        if (modal && e.target === modal) {
            modal.classList.remove('active');
        }
    });
});