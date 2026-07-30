document.addEventListener('DOMContentLoaded', () => {
    const activityModal = document.getElementById('activity-modal');
    const activityClose = document.getElementById('close-activity-modal');
    const activityTitle = document.getElementById('modal-title');
    const activityTime = document.getElementById('modal-time');
    const activityDocent = document.getElementById('modal-docent');
    const activityLocation = document.getElementById('modal-location');
    const activityCapacity = document.getElementById('modal-capacity');

    const ACTIVIDADES = window.ACTIVIDADES || {};

    // Eventos del calendario
    document.querySelectorAll('.event-tag[data-id]').forEach(tag => {
        tag.addEventListener('click', (e) => {
            e.stopPropagation();
            const id = tag.getAttribute('data-id');
            const data = ACTIVIDADES[id] || null;

            if (!data) {
                console.warn('Actividad no encontrada para id', id);
                return;
            }

            activityTitle.textContent = data.titulo || 'Sin título';
            activityTime.textContent = data.horario || '-';
            activityDocent.textContent = data.docente || '-';
            activityLocation.textContent = data.salon || '-';
            activityCapacity.textContent = data.capacidad || '-';
            if (activityModal) activityModal.classList.add('active');
        });
    });

    if (activityClose) {
        activityClose.addEventListener('click', () => activityModal.classList.remove('active'));
    }

    window.addEventListener('click', (e) => {
        if (e.target === activityModal) activityModal.classList.remove('active');
    });

    // Logs
    const logModal = document.getElementById('log-modal');
    const logClose = document.getElementById('close-log-modal');
    const logTitle = document.getElementById('log-title');
    const logBody = document.getElementById('log-body');

    document.querySelectorAll('.log-item').forEach(item => {
        item.addEventListener('click', () => {
            const details = item.dataset.details;
            logTitle.textContent = item.querySelector('.log-title')?.textContent || 'Registro';
            logBody.textContent = details ? JSON.stringify(JSON.parse(details), null, 2) : 'Sin detalles';
            if (logModal) logModal.classList.add('active');
        });
    });

    if (logClose) logClose.addEventListener('click', () => logModal.classList.remove('active'));
});
