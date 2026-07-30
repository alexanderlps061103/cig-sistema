document.addEventListener('DOMContentLoaded', () => {
    // === 1. REFERENCIAS A ELEMENTOS ===
    const createModal = document.getElementById('create-modal');
    const editModal = document.getElementById('edit-modal');
    const toggleModal = document.getElementById('delete-modal');

    // === 2. GESTIÓN DE APERTURA (CREAR) ===
    const btnOpenCreate = document.getElementById('btn-open-create');
    if (btnOpenCreate) {
        btnOpenCreate.onclick = (e) => {
            e.preventDefault();
            const form = createModal.querySelector('form');
            if (form) form.reset(); // Limpia el formulario
            createModal.classList.add('active');
        };
    }

    // === 3. FUNCIÓN PARA EDITAR (GLOBAL) ===
    window.openEditModal = function(id, nombre, descripcion) {
        const form = document.getElementById('edit-form');
        const inputNombre = document.getElementById('edit-nombre');
        const inputDesc = document.getElementById('edit-descripcion');

        if (form) {
            form.action = `/rector/carreras/${id}`;
            inputNombre.value = nombre;
            inputDesc.value = (descripcion !== 'undefined' && descripcion !== 'null') ? descripcion : '';
            editModal.classList.add('active');
        }
    };

    // === 4. FUNCIÓN PARA HABILITAR / DESHABILITAR (GLOBAL) ===
    window.openToggleModal = function(id, nombre, isActive) {
        const form = document.getElementById('toggle-form');
        const itemName = document.getElementById('toggle-item-name');
        const titleText = document.getElementById('toggle-title');
        const confirmBtn = document.getElementById('btn-confirm-toggle');

        if (form && toggleModal) {
            // Asignar la ruta al formulario
            form.action = `/rector/carreras/${id}/toggle`;

            // Poner el nombre de la carrera en el texto
            if (itemName) itemName.innerText = nombre;

            // Lógica de colores y textos:
            // Si isActive es 1 o true, la carrera está encendida -> Opción: Inhabilitar
            if (isActive == 1 || isActive == true || isActive == '1') {
                titleText.innerText = "Inhabilitar Carrera";
                confirmBtn.innerText = "Confirmar Inhabilitación";
                confirmBtn.className = "modal-btn btn-danger"; // Rojo
            } else {
                // Si está inactiva -> Opción: Habilitar
                titleText.innerText = "Habilitar Carrera";
                confirmBtn.innerText = "Confirmar Habilitación";
                confirmBtn.className = "modal-btn btn-primary"; // Azul
                confirmBtn.style.backgroundColor = "#23378c";
            }

            toggleModal.classList.add('active');
        }
    };

    // === 5. FILTRADO Y BÚSQUEDA EN TIEMPO REAL ===
    const searchInput = document.getElementById('search-carrera');
    const filterStatus = document.getElementById('filter-status');

    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const statusSelected = filterStatus.value; // 'all', '1', '0'
        const rows = document.querySelectorAll('.data-row');

        rows.forEach(row => {
            const nombre = row.dataset.nombre || '';
            const desc = row.dataset.descripcion || '';
            const estado = row.dataset.estado || ''; // '1' o '0'

            const matchesSearch = nombre.includes(query) || desc.includes(query);
            const matchesStatus = (statusSelected === 'all') || (estado === statusSelected);

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (filterStatus) filterStatus.addEventListener('change', filterTable);

    // === 6. AUTO-OCULTAR ALERTAS (TOASTS) ===
    const alerts = document.querySelectorAll('.alert-message');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(100%)';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});
