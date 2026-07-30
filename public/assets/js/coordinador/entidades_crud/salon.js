document.addEventListener('DOMContentLoaded', () => {
    // === GESTIÓN DE MODALES ===
    const createModal = document.getElementById('create-modal');
    const editModal = document.getElementById('edit-modal');
    const deleteModal = document.getElementById('delete-modal');
    
    const btnOpenCreate = document.getElementById('btn-open-create');
    const btnCloseCreate = document.getElementById('btn-close-create-modal');
    const btnCancelCreate = document.getElementById('btn-cancel-create');
    
    const btnCloseEdit = document.getElementById('btn-close-edit-modal');
    const btnCancelEdit = document.getElementById('btn-cancel-edit');
    
    const btnCloseDelete = document.getElementById('btn-close-delete-modal');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');

    // === ELEMENTOS DE FORMULARIOS ===
    const createForm = document.getElementById('create-form');
    const editForm = document.getElementById('edit-form');
    const deleteForm = document.getElementById('delete-form');

    const createNombre = document.getElementById('create-nombre');
    const editNombre = document.getElementById('edit-nombre');

    // === RESTAURACIÓN DE ACCIÓN TRAS FALLO DE VALIDACIÓN ===
    const hiddenIdInput = document.getElementById('edit-id');
    if (editModal && editModal.classList.contains('active') && hiddenIdInput && hiddenIdInput.value) {
        if (editForm) {
            editForm.action = '/coordinador/entidades_crud/salon/' + hiddenIdInput.value;
        }
    }

    // Cierre seguro al hacer clic fuera del modal (overlay)
    const setupOverlayClose = (modal) => {
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.remove('active');
            });
        }
    };
    [createModal, editModal, deleteModal].forEach(setupOverlayClose);

    // Abrir Modal de Creación
    if (btnOpenCreate) {
        btnOpenCreate.addEventListener('click', () => {
            if (createForm) {
                createForm.reset();
            }
            resetStyles(createNombre);
            if (createModal) createModal.classList.add('active');
        });
    }

    // Cerrar Modal Creación
    const closeCreate = () => {
        if (createModal) createModal.classList.remove('active');
    };
    if (btnCloseCreate) btnCloseCreate.addEventListener('click', closeCreate);
    if (btnCancelCreate) btnCancelCreate.addEventListener('click', closeCreate);

    // Cerrar Modal Edición
    const closeEdit = () => {
        if (editModal) editModal.classList.remove('active');
    };
    if (btnCloseEdit) btnCloseEdit.addEventListener('click', closeEdit);
    if (btnCancelEdit) btnCancelEdit.addEventListener('click', closeEdit);

    // Abrir Modal de Edición (Carga de datos dinámicos)
    window.openEditModal = function(id, nombre, capacidad, estado) {
        resetStyles(editNombre);

        if (editForm) {
            // CORREGIDO: Se cambia el prefijo a 'entidades_crud'
            editForm.action = '/coordinador/entidades_crud/salon/' + id;
        }

        // CORREGIDO: Se inyecta el ID en el input oculto para persistencia ante errores de validación
        const inputId = document.getElementById('edit-id');
        if (inputId) {
            inputId.value = id;
        }

        const inputNombre = document.getElementById('edit-nombre');
        const inputCapacidad = document.getElementById('edit-capacidad');
        const inputEstado = document.getElementById('edit-estado');

        if (inputNombre) inputNombre.value = nombre;
        if (inputCapacidad) inputCapacidad.value = capacidad;
        if (inputEstado) inputEstado.value = estado; 

        if (editModal) editModal.classList.add('active');
    };

    // Abrir Modal de Eliminación
    window.openDeleteModal = function(id, nombre) {
        if (deleteForm) {
            // CORREGIDO: Se cambia el prefijo a 'entidades_crud'
            deleteForm.action = '/coordinador/entidades_crud/salon/' + id;
        }

        const txtName = document.getElementById('delete-item-name');
        if (txtName) {
            txtName.innerText = nombre;
        }

        if (deleteModal) deleteModal.classList.add('active');
    };

    // Cerrar Modal de Eliminación
    const closeDelete = () => {
        if (deleteModal) deleteModal.classList.remove('active');
    };
    if (btnCloseDelete) btnCloseDelete.addEventListener('click', closeDelete);
    if (btnCancelDelete) btnCancelDelete.addEventListener('click', closeDelete);


    // ==========================================================================
    // SISTEMA DE VALIDACIÓN EN TIEMPO REAL (ASISTENCIA VISUAL DE BORDES)
    // ==========================================================================

    function applyStyle(input, state) {
        if (!input) return;
        input.classList.remove('val-red', 'val-yellow', 'val-green');
        if (state === 'red') {
            input.classList.add('val-red');
        } else if (state === 'yellow') {
            input.classList.add('val-yellow');
        } else if (state === 'green') {
            input.classList.add('val-green');
        }
    }

    function resetStyles(input) {
        if (input) {
            input.classList.remove('val-red', 'val-yellow', 'val-green');
        }
    }

    // Regla de validación para el nombre del salón (Asistencia visual)
    function validateNombre(value) {
        const trimmed = value.trim();
        if (trimmed === '') {
            return 'red';
        }
        // Permite letras, números, espacios y los caracteres especiales: .,()\-#
        const hasInvalidChars = /[^a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s.,()\-#]/.test(trimmed);
        if (hasInvalidChars) {
            return 'red';
        }
        if (trimmed.length < 3) {
            return 'yellow';
        }
        return 'green';
    }

    // Escuchadores de eventos para inputs (Creación y Edición)
    if (createNombre) {
        createNombre.addEventListener('input', () => {
            applyStyle(createNombre, validateNombre(createNombre.value));
        });
        createNombre.addEventListener('blur', () => {
            applyStyle(createNombre, validateNombre(createNombre.value));
        });
    }

    if (editNombre) {
        editNombre.addEventListener('input', () => {
            applyStyle(editNombre, validateNombre(editNombre.value));
        });
        editNombre.addEventListener('blur', () => {
            applyStyle(editNombre, validateNombre(editNombre.value));
        });
    }


    // === SISTEMA DE BÚSQUEDA Y FILTRADO DINÁMICO EN TIEMPO REAL ===
    const searchInput = document.getElementById('search-salon');
    const filterStatus = document.getElementById('filter-status');
    const dataRows = document.querySelectorAll('.data-row');
    const noResultsRow = document.getElementById('no-results-row');

    function applyFilters() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const statusSelected = filterStatus ? filterStatus.value : 'all';
        let visibleRows = 0;

        dataRows.forEach(row => {
            const nombre = row.getAttribute('data-nombre') || '';
            const estado = row.getAttribute('data-estado') || '';

            const matchesSearch = nombre.includes(query);
            const matchesStatus = (statusSelected === 'all') || (estado === statusSelected);

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResultsRow) {
            noResultsRow.style.display = (visibleRows === 0 && dataRows.length > 0) ? '' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    if (filterStatus) {
        filterStatus.addEventListener('change', applyFilters);
    }


    // === EFECTO DE DESVANECIDO PARA ALERTAS (FADE OUT) ===
    const alertMessages = document.querySelectorAll('.alert-message');
    alertMessages.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-15px)';
            
            setTimeout(() => {
                alert.remove();
            }, 600);
        }, 4000); 
    });
});