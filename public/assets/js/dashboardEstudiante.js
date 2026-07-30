document.addEventListener('DOMContentLoaded', () => {
    // === ELEMENTOS DEL MODAL QR ===
    const qrModal = document.getElementById('qr-modal');
    const closeQrModalBtn = document.getElementById('close-qr-modal-btn');
    const closeQrFooterBtn = document.getElementById('close-qr-footer-btn');
    const qrTitle = document.getElementById('qr-activity-title');
    const qrLocation = document.getElementById('qr-activity-location');

    // Datos de asistencia simulados para el QR
    const qrActivities = {
        "1": {
            titulo: "Taller: Introducción a Redes de Computadoras",
            lugar: "Ubicación: Laboratorio A"
        },
        "2": {
            titulo: "Foro: Liderazgo y Planificación Institucional",
            lugar: "Ubicación: Auditorio Principal"
        },
        "3": {
            titulo: "Seminario: Herramientas de IA Aplicada",
            lugar: "Ubicación: Salón de Usos Múltiples"
        }
    };

    // Al hacer clic en los botones "Mostrar QR" de la tabla
    const showQrBtns = document.querySelectorAll('.show-qr-btn');
    
    showQrBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const actId = btn.getAttribute('data-id');
            const data = qrActivities[actId];
            
            // Se valida que existan tanto los datos como los elementos del DOM antes de usarlos
            if (data) {
                if (qrTitle) qrTitle.textContent = data.titulo;
                if (qrLocation) qrLocation.textContent = data.lugar;
                if (qrModal) qrModal.classList.add('active');
            }
        });
    });

    // Funciones de cierre del Modal (todas con guardas de seguridad)
    if (closeQrModalBtn && qrModal) {
        closeQrModalBtn.addEventListener('click', () => {
            qrModal.classList.remove('active');
        });
    }

    if (closeQrFooterBtn && qrModal) {
        closeQrFooterBtn.addEventListener('click', () => {
            qrModal.classList.remove('active');
        });
    }

    // Cerrar al hacer clic fuera del recuadro blanco del modal
    window.addEventListener('click', (e) => {
        if (qrModal && e.target === qrModal) {
            qrModal.classList.remove('active');
        }
    });
});