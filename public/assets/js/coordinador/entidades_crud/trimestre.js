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
            editForm.action = '/coordinador/planificacion/trimestres/' + hiddenIdInput.value + '/update';
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
    window.openEditModal = function(id, nombre, fechaInicio, fechaFin, idPlanificacion) {
        resetStyles(editNombre);

        if (editForm) {
            editForm.action = '/coordinador/planificacion/trimestres/' + id + '/update';
        }

        // Se inyecta el ID en el input oculto para persistencia de la acción
        const inputId = document.getElementById('edit-id');
        if (inputId) {
            inputId.value = id;
        }

        const inputNombre = document.getElementById('edit-nombre');
        const inputFechaInicio = document.getElementById('edit-fecha-inicio');
        const inputFechaFin = document.getElementById('edit-fecha-fin');
        const inputPlanificacion = document.getElementById('edit-planificacion');

        if (inputNombre) inputNombre.value = nombre;
        if (inputFechaInicio) inputFechaInicio.value = fechaInicio;
        if (inputFechaFin) inputFechaFin.value = fechaFin;
        if (inputPlanificacion) inputPlanificacion.value = idPlanificacion; 

        if (editModal) editModal.classList.add('active');
    };

    // Abrir Modal de Eliminación
    window.openDeleteModal = function(id, nombre) {
        if (deleteForm) {
            deleteForm.action = '/coordinador/planificacion/trimestres/' + id + '/destroy';
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
    // SISTEMA DE VALIDACIÓN EN TIEMPO REAL (SOLO ASISTENCIA DE COLOR)
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

    // Validación interactiva del nombre
    function validateNombre(value) {
        const trimmed = value.trim();
        if (trimmed === '') {
            return 'red';
        }
        const hasInvalidChars = /[^a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s.,()\-#]/.test(trimmed);
        if (hasInvalidChars) {
            return 'red';
        }
        if (trimmed.length < 3) {
            return 'yellow';
        }
        return 'green';
    }

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
    const searchInput = document.getElementById('search-trimestre');
    const filterPlanificacion = document.getElementById('filter-planificacion');
    const dataRows = document.querySelectorAll('.data-row');
    const noResultsRow = document.getElementById('no-results-row');

    function applyFilters() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const planSelected = filterPlanificacion ? filterPlanificacion.value : 'all';
        let visibleRows = 0;

        dataRows.forEach(row => {
            const nombre = row.getAttribute('data-nombre') || '';
            const plan = row.getAttribute('data-planificacion') || '';

            const matchesSearch = nombre.includes(query);
            const matchesPlan = (planSelected === 'all') || (plan === planSelected);

            if (matchesSearch && matchesPlan) {
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
    if (filterPlanificacion) {
        filterPlanificacion.addEventListener('change', applyFilters);
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