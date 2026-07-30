document.addEventListener('DOMContentLoaded', () => {
    const createModal = document.getElementById('create-modal');
    const editModal = document.getElementById('edit-modal');
    const toggleModal = document.getElementById('delete-modal');

    // Botones Abrir/Cerrar
    const setupModal = (btnId, modal, isReset = false) => {
        const btn = document.getElementById(btnId);
        if (btn) btn.onclick = () => {
            if (isReset) document.getElementById('create-form').reset();
            modal.classList.add('active');
        };
    };

    setupModal('btn-open-create', createModal, true);
    document.getElementById('btn-cancel-create').onclick = () => createModal.classList.remove('active');
    document.getElementById('btn-cancel-edit').onclick = () => editModal.classList.remove('active');
    document.getElementById('btn-cancel-delete').onclick = () => toggleModal.classList.remove('active');

    // Función Editar
    window.openEditModal = function(id, nombre, descripcion) {
        const form = document.getElementById('edit-form');
        form.action = `/rector/profesiones/${id}`;
        document.getElementById('edit-nombre').value = nombre;
        document.getElementById('edit-descripcion').value = (descripcion !== 'undefined' && descripcion) ? descripcion : '';
        editModal.classList.add('active');
    };

    // Función Toggle (Habilitar/Inhabilitar)
    window.openToggleModal = function(id, nombre, isActive) {
        const form = document.getElementById('toggle-form');
        const title = document.getElementById('toggle-title-text');
        const btn = document.getElementById('btn-confirm-toggle');

        form.action = `/rector/profesiones/${id}/toggle`;
        document.getElementById('toggle-item-name').innerText = nombre;

        if (isActive) {
            title.innerText = "¿Inhabilitar esta profesión?";
            btn.className = "modal-btn btn-danger";
            btn.innerText = "Sí, inhabilitar";
        } else {
            title.innerText = "¿Habilitar esta profesión?";
            btn.className = "modal-btn btn-primary";
            btn.innerText = "Sí, habilitar";
        }
        toggleModal.classList.add('active');
    };

    // Filtros y Alertas (Igual que en Cargos)
    const searchInput = document.getElementById('search-profesion');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.data-row').forEach(row => {
                const text = row.getAttribute('data-nombre') + row.getAttribute('data-descripcion');
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
