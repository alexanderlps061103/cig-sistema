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

    const createNombre = document.getElementById('create-nombre');
    const editNombre = document.getElementById('edit-nombre');

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
    if (btnCancelCreate) btnCancelCreate.addEventListener('click', closeCreate);

    // Cerrar Modal Edición
    const closeEdit = () => {
        if (editModal) editModal.classList.remove('active');
    };
    if (btnCancelEdit) btnCancelEdit.addEventListener('click', closeEdit);

    // Abrir Modal de Edición (Asigna ruta e inyecta datos)
    window.openEditModal = function(idSello, nombre, imagenUrl, estado) {
        resetStyles(editNombre);

        if (editForm) {
            // CORREGIDO: Se cambia el prefijo a 'entidades_crud'
            editForm.action = '/coordinador/entidades_crud/sello/' + idSello; 
        }

        const inputNombre = document.getElementById('edit-nombre');
        const inputEstado = document.getElementById('edit-estado');
        const previewContainer = document.getElementById('edit-image-preview-container');
        const previewImg = document.getElementById('edit-preview');

        if (inputNombre) inputNombre.value = nombre;
        if (inputEstado) inputEstado.value = estado; 

        // Gestiona la previsualización del sello guardado anteriormente
        if (previewContainer && previewImg) {
            if (imagenUrl && imagenUrl !== '') {
                previewImg.src = imagenUrl;
                previewContainer.style.display = 'block';
            } else {
                previewImg.src = '';
                previewContainer.style.display = 'none';
            }
        }
        
        if (editModal) editModal.classList.add('active');
    };

    // Abrir Modal de Inhabilitación (Asigna ruta e inyecta nombre)
    window.openDeleteModal = function(idSello, nombre) {
        if (deleteForm) {
            // CORREGIDO: Se cambia el prefijo a 'entidades_crud'
            deleteForm.action = '/coordinador/entidades_crud/sello/' + idSello; 
        }

        const txtName = document.getElementById('delete-item-name');
        if (txtName) {
            txtName.innerText = nombre;
        }
        
        if (deleteModal) deleteModal.classList.add('active');
    };

    // Cerrar Modal de Inhabilitación
    const closeDelete = () => {
        if (deleteModal) deleteModal.classList.remove('active');
    };
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

    // Regla de validación para el nombre del sello (Asistencia visual)
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
    const searchInput = document.getElementById('search-sello');
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