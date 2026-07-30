document.addEventListener('DOMContentLoaded', () => {
    const createModal = document.getElementById('create-modal');
    const editModal = document.getElementById('edit-modal');

    const btnOpenCreate = document.getElementById('btn-open-create');
    const btnCancelCreate = document.getElementById('btn-cancel-create');
    const btnCancelCreate2 = document.getElementById('btn-cancel-create-2');
    const btnCancelEdit = document.getElementById('btn-cancel-edit');

    const editForm = document.getElementById('edit-form');

    // === GESTIÓN DE MODALES ===
    if (btnOpenCreate) {
        btnOpenCreate.addEventListener('click', () => {
            createModal.classList.add('active');
        });
    }

    const closeCreate = () => createModal.classList.remove('active');
    if (btnCancelCreate) btnCancelCreate.addEventListener('click', closeCreate);
    if (btnCancelCreate2) btnCancelCreate2.addEventListener('click', closeCreate);

    const closeEdit = () => editModal.classList.remove('active');
    if (btnCancelEdit) btnCancelEdit.addEventListener('click', closeEdit);

    window.openEditModal = function(id, nombre, ubicacion, capacidad, descripcion, activo) {
        // Ajustamos la acción del formulario a la ruta de Laravel
        if (editForm) {
            editForm.action = '/coordinador/planificacion/periodos/' + id; // Ajusta según tu web.php
        }

        document.getElementById('edit-nombre').value = nombre;
        document.getElementById('edit-ubicacion').value = ubicacion;
        document.getElementById('edit-capacidad').value = capacidad;

        editModal.classList.add('active');
    };

    // === FILTRADO EN TIEMPO REAL ===
    const searchInput = document.getElementById('search-salon');
    const filterStatus = document.getElementById('filter-status');
    const dataRows = document.querySelectorAll('.data-row');

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const statusSelected = filterStatus.value;

        dataRows.forEach(row => {
            const nombre = row.getAttribute('data-nombre');
            const ubicacion = row.getAttribute('data-ubicacion');
            const estado = row.getAttribute('data-estado');

            const matchesSearch = nombre.includes(query) || ubicacion.includes(query);
            const matchesStatus = (statusSelected === 'all') || (estado === statusSelected);

            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);

    // === AUTO-HIDE ALERTS ===
    const alertMessages = document.querySelectorAll('.alert-message');
    alertMessages.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(100%)';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});
