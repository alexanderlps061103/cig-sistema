document.addEventListener('DOMContentLoaded', () => {
    // --- Referencias a los Modales ---
    const createModal = document.getElementById('create-modal');
    const editModal = document.getElementById('edit-modal');
    const toggleModal = document.getElementById('delete-modal'); // Usamos el ID delete-modal por tu CSS

    // --- Botón Abrir Crear ---
    const btnOpenCreate = document.getElementById('btn-open-create');
    if (btnOpenCreate) {
        btnOpenCreate.onclick = () => {
            document.getElementById('create-form').reset();
            createModal.classList.add('active');
        };
    }

    // --- Funciones para Cerrar (Botones Cancelar) ---
    window.closeAllModals = function() {
        createModal.classList.remove('active');
        editModal.classList.remove('active');
        toggleModal.classList.remove('active');
    };

    // Asignar cierre a los botones de cancelar
    const closeButtons = ['btn-cancel-create', 'btn-cancel-edit', 'btn-cancel-delete'];
    closeButtons.forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.onclick = closeAllModals;
    });

    // --- Función Editar (Global para el onclick del Blade) ---
    window.openEditModal = function(id, nombre) {
        const form = document.getElementById('edit-form');
        form.action = `/rector/cargos/${id}`;
        document.getElementById('edit-nombre-cargo').value = nombre;
        editModal.classList.add('active');
    };

    // --- Función Toggle (Global para el onclick del Blade) ---
    window.openToggleModal = function(id, nombre, isActive) {
        const form = document.getElementById('delete-form');
        const title = document.getElementById('toggle-title');
        const btn = document.getElementById('btn-confirm-toggle');

        form.action = `/rector/cargos/${id}/toggle`;
        document.getElementById('delete-item-name').innerText = nombre;

        if (isActive) {
            title.innerText = "¿Desea inhabilitar este cargo?";
            btn.innerText = "Sí, inhabilitar";
            btn.className = "modal-btn btn-danger";
        } else {
            title.innerText = "¿Desea habilitar este cargo?";
            btn.innerText = "Sí, habilitar";
            btn.className = "modal-btn btn-primary";
        }
        toggleModal.classList.add('active');
    };

    // --- Buscador en tiempo real ---
    const searchInput = document.getElementById('search-cargo');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.data-row').forEach(row => {
                const text = row.getAttribute('data-nombre').toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
