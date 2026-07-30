document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('activity-search-input');
    const activitiesContainer = document.getElementById('activities-container');
    const cards = document.querySelectorAll('.activity-card');
    const noResultsRow = document.getElementById('no-results-row');

    // 1. Buscador en tiempo real de actividades
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let visibleCards = 0;

            cards.forEach(card => {
                const nombre = card.getAttribute('data-nombre').toLowerCase();
                const descripcion = card.getAttribute('data-descripcion').toLowerCase();

                if (nombre.includes(query) || descripcion.includes(query)) {
                    card.style.display = 'flex';
                    visibleCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCards === 0 && query !== '') {
                noResultsRow.style.display = 'flex';
                if (activitiesContainer) activitiesContainer.style.display = 'none';
            } else {
                noResultsRow.style.display = 'none';
                if (activitiesContainer) activitiesContainer.style.display = 'grid';
            }
        });
    }

    // 2. Control de clicks en botones "Ver Detalles"
    document.body.addEventListener('click', function (e) {
        if (e.target.closest('.btn-view-details')) {
            const card = e.target.closest('.activity-card');
            if (card) {
                openDetailsModal(card);
            }
        }
    });
});

// Función para abrir modal de detalles
function openDetailsModal(card) {
    const id = card.getAttribute('data-id');
    const nombre = card.getAttribute('data-nombre');
    const descripcion = card.getAttribute('data-descripcion');
    const fecha = card.getAttribute('data-fecha');
    const horario = card.getAttribute('data-horario');
    const ubicacion = card.getAttribute('data-ubicacion');
    const modalidad = card.getAttribute('data-modalidad');
    const tipo = card.getAttribute('data-tipo');
    const inscrito = card.getAttribute('data-inscrito') === 'true';

    // Poblar campos del modal
    document.getElementById('details-modal-title').innerText = nombre;
    document.getElementById('details-modal-tipo').innerText = tipo;
    document.getElementById('details-modal-fecha').innerText = fecha;
    document.getElementById('details-modal-horario').innerText = horario;
    document.getElementById('details-modal-ubicacion').innerText = ubicacion;
    document.getElementById('details-modal-modalidad').innerText = modalidad;
    document.getElementById('details-modal-descripcion').innerText = descripcion || 'Sin descripción disponible.';

    const footer = document.getElementById('details-modal-footer');
    footer.innerHTML = ''; // Limpiar botones anteriores

    // Botón de cerrar para todos los casos
    const btnClose = document.createElement('button');
    btnClose.type = 'button';
    btnClose.className = 'btn-action btn-cancel';
    btnClose.innerText = 'Cerrar';
    btnClose.onclick = closeDetailsModal;
    footer.appendChild(btnClose);

    if (inscrito) {
        // Estado: Inscrito
        const btnInscrito = document.createElement('button');
        btnInscrito.type = 'button';
        btnInscrito.className = 'btn-action';
        btnInscrito.style.backgroundColor = 'var(--color-success)';
        btnInscrito.style.color = 'white';
        btnInscrito.style.cursor = 'default';
        btnInscrito.disabled = true;
        btnInscrito.innerHTML = '<i class="fa-solid fa-check"></i> Ya estás inscrito';
        footer.appendChild(btnInscrito);
    } else {
        // Estado: No inscrito
        const btnInscribir = document.createElement('button');
        btnInscribir.type = 'button';
        btnInscribir.className = 'btn-action';
        btnInscribir.style.backgroundColor = 'var(--color-brand-blue)';
        btnInscribir.style.color = 'white';
        btnInscribir.innerText = 'Inscribirme';
        btnInscribir.onclick = function () {
            closeDetailsModal();
            intentarInscripcion(id, nombre);
        };
        footer.appendChild(btnInscribir);
    }

    document.getElementById('activity-details-modal').classList.add('active');
}

// Lógica de intento de inscripción: Comprobación de perfil
function intentarInscripcion(id, nombre) {
    const profile = window.userProfile || {};
    const perfilIncompleto = !profile.cedula || !profile.telefono || !profile.nombres || !profile.apellidos;

    if (perfilIncompleto) {
        // Abre el modal para completar los datos
        openProfileModal(id, nombre);
    } else {
        // Genera y envía un formulario POST directamente
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/publico/inscribir/${id}`;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }
}

// Abrir modal de complementar perfil
function openProfileModal(id, nombre) {
    document.getElementById('modal-activity-name').innerText = nombre;
    
    const form = document.getElementById('quick-profile-form');
    if (form) {
        form.action = `/publico/inscribir/${id}`;
    }
    
    document.getElementById('quick-profile-modal').classList.add('active');
}

// Cerrar modal de detalles
function closeDetailsModal() {
    document.getElementById('activity-details-modal').classList.remove('active');
}

// Cerrar modal de perfil
function closeProfileModal() {
    document.getElementById('quick-profile-modal').classList.remove('active');
}