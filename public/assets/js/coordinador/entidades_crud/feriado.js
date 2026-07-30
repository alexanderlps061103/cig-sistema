document.addEventListener('DOMContentLoaded', () => {
    // === GESTIÓN DE MODALES ===
    const createModal = document.getElementById('create-modal');
    const editModal = document.getElementById('edit-modal');
    const deleteModal = document.getElementById('delete-modal');
    
    const btnOpenCreate = document.getElementById('btn-open-create');
    const btnCancelCreate = document.getElementById('btn-cancel-create');
    const btnCancelEdit = document.getElementById('btn-cancel-edit');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');

    // === ELEMENTOS DE FORMULARIOS ===
    const createForm = document.getElementById('create-form');
    const editForm = document.getElementById('edit-form');
    const deleteForm = document.getElementById('delete-form');

    const createDescripcion = document.getElementById('create-descripcion');
    const editDescripcion = document.getElementById('edit-descripcion');

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
            resetStyles(createDescripcion);
            if (createModal) createModal.classList.add('active');
        });
    }

    // Cerrar Modal Creación
    const closeCreate = () => {
        if (createModal) createModal.classList.remove('active');
    };
    if (btnCancelCreate) btnCancelCreate.addEventListener('click', closeCreate);

    // Cerrar Modal Edición
    const closeEdit = () => {
        if (editModal) editModal.classList.remove('active');
    };
    if (btnCancelEdit) btnCancelEdit.addEventListener('click', closeEdit);

    // Abrir Modal de Edición (Asigna ruta y rellena datos)
    window.openEditModal = function(idFeriado, fecha, descripcion, recurrente) {
        resetStyles(editDescripcion);

        if (editForm) {
            editForm.action = '/coordinador/estructura/feriado/' + idFeriado; 
        }

        const inputFecha = document.getElementById('edit-fecha');
        const inputDescripcion = document.getElementById('edit-descripcion');
        const inputRecurrente = document.getElementById('edit-recurrente');

        if (inputFecha) inputFecha.value = fecha;
        if (inputDescripcion) inputDescripcion.value = (descripcion !== 'undefined' && descripcion) ? descripcion : '';
        if (inputRecurrente) inputRecurrente.value = recurrente; 
        
        if (editModal) editModal.classList.add('active');
    };

    // Abrir Modal de Eliminación
    window.openDeleteModal = function(idFeriado, descripcion) {
        if (deleteForm) {
            deleteForm.action = '/coordinador/estructura/feriado/' + idFeriado; 
        }

        const txtName = document.getElementById('delete-item-name');
        if (txtName) {
            txtName.innerText = descripcion;
        }
        
        if (deleteModal) deleteModal.classList.add('active');
    };

    // Cerrar Modal de Eliminación
    const closeDelete = () => {
        if (deleteModal) deleteModal.classList.remove('active');
    };
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

    // Regla de validación para el motivo/descripción del feriado
    function validateDescripcion(value) {
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

    // Escuchadores de eventos para campos
    if (createDescripcion) {
        createDescripcion.addEventListener('input', () => {
            applyStyle(createDescripcion, validateDescripcion(createDescripcion.value));
        });
        createDescripcion.addEventListener('blur', () => {
            applyStyle(createDescripcion, validateDescripcion(createDescripcion.value));
        });
    }

    if (editDescripcion) {
        editDescripcion.addEventListener('input', () => {
            applyStyle(editDescripcion, validateDescripcion(editDescripcion.value));
        });
        editDescripcion.addEventListener('blur', () => {
            applyStyle(editDescripcion, validateDescripcion(editDescripcion.value));
        });
    }


    // === SISTEMA DE BÚSQUEDA Y FILTRADO DINÁMICO EN TIEMPO REAL ===
    const searchInput = document.getElementById('search-feriado');
    const filterRecurrente = document.getElementById('filter-recurrente');
    const dataRows = document.querySelectorAll('.data-row');
    const noResultsRow = document.getElementById('no-results-row');

    function applyFilters() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const recurrenteSelected = filterRecurrente ? filterRecurrente.value : 'all';
        let visibleRows = 0;

        dataRows.forEach(row => {
            const descripcion = row.getAttribute('data-descripcion') || '';
            const fecha = row.getAttribute('data-fecha') || '';
            const recurrente = row.getAttribute('data-recurrente') || '';

            const matchesSearch = descripcion.includes(query) || fecha.includes(query);
            const matchesRecurrente = (recurrenteSelected === 'all') || (recurrente === recurrenteSelected);

            if (matchesSearch && matchesRecurrente) {
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
    if (filterRecurrente) {
        filterRecurrente.addEventListener('change', applyFilters);
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