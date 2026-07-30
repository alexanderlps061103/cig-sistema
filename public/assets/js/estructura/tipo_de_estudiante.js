document.addEventListener('DOMContentLoaded', () => {
    const createModal = document.getElementById('create-modal');
    const editModal = document.getElementById('edit-modal');
    const deleteModal = document.getElementById('delete-modal');

    // Botones
    document.getElementById('btn-open-create').onclick = () => createModal.classList.add('active');
    document.getElementById('btn-cancel-create').onclick = () => createModal.classList.remove('active');
    document.getElementById('btn-cancel-edit').onclick = () => editModal.classList.remove('active');
    document.getElementById('btn-cancel-delete').onclick = () => deleteModal.classList.remove('active');

    // Función Editar
    window.openEditModal = function(id, nombre, estado) {
        document.getElementById('edit-form').action = `/rector/tipo-estudiantes/${id}`;
        document.getElementById('edit-nombre').value = nombre;
        document.getElementById('edit-estado').value = estado;
        editModal.classList.add('active');
    };

    // Función Desactivar (Toggle)
    window.openDeleteModal = function(id, nombre) {
        document.getElementById('delete-form').action = `/rector/tipo-estudiantes/${id}/toggle`;
        document.getElementById('delete-item-name').innerText = nombre;
        deleteModal.classList.add('active');
    };

    // Búsqueda en tiempo real
    const searchInput = document.getElementById('search-tipo-estudiante');
    const statusFilter = document.getElementById('filter-status');
    const rows = document.querySelectorAll('.data-row');

    function filter() {
        const text = searchInput.value.toLowerCase();
        const status = statusFilter.value;

        rows.forEach(row => {
            const rowName = row.dataset.nombre;
            const rowStatus = row.dataset.estado;
            const matchesText = rowName.includes(text);
            const matchesStatus = status === 'all' || rowStatus === status;
            row.style.display = (matchesText && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filter);
    statusFilter.addEventListener('change', filter);
});
