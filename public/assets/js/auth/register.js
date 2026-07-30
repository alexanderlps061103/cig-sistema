document.addEventListener('DOMContentLoaded', function() {
    const tipoRegistro = document.getElementById('tipo_register_select');
    const seccionUney = document.getElementById('seccion_uney');
    const seccionAspirante = document.getElementById('seccion_aspirante');

    // ==========================================
    // 1. Mostrar/Ocultar campos según el tipo de registro
    // ==========================================
    function toggleFields() {
        const value = tipoRegistro.value;

        seccionUney.style.display = 'none';
        seccionAspirante.style.display = 'none';

        if (value === 'estudiante_regular') {
            seccionUney.style.display = 'block';
        } else if (value === 'aspirante_docente') {
            seccionAspirante.style.display = 'block';
        }
    }

    tipoRegistro.addEventListener('change', toggleFields);
    toggleFields(); // Ejecución inmediata para recuperar estado de validación

    // ==========================================
    // 2. Lógica del Buscador Dinámico de Carreras
    // ==========================================
    const carreraInput = document.getElementById('carrera_search_input');
    const carreraHidden = document.getElementById('carrera_id_hidden');
    const carreraDropdown = document.getElementById('carrera_dropdown_list');
    const carreraOptions = carreraDropdown ? carreraDropdown.querySelectorAll('.search-select-option') : [];

    if (carreraInput) {
        carreraInput.addEventListener('focus', function() {
            carreraDropdown.style.display = 'block';
        });

        carreraInput.addEventListener('input', function() {
            const filter = carreraInput.value.toLowerCase();
            carreraDropdown.style.display = 'block';
            carreraOptions.forEach(option => {
                const text = option.textContent.toLowerCase();
                if (text.includes(filter)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });

        carreraOptions.forEach(option => {
            option.addEventListener('click', function() {
                carreraInput.value = option.textContent.trim();
                carreraHidden.value = option.getAttribute('data-value');
                carreraDropdown.style.display = 'none';
            });
        });

        // Cerrar dropdown si se hace click afuera
        document.addEventListener('click', function(e) {
            if (!carreraInput.contains(e.target) && !carreraDropdown.contains(e.target)) {
                carreraDropdown.style.display = 'none';
            }
        });
    }

    // ==========================================
    // 3. Lógica del Buscador Dinámico de Profesiones
    // ==========================================
    const profesionInput = document.getElementById('profesion_search_input');
    const profesionHidden = document.getElementById('profesion_id_hidden');
    const profesionDropdown = document.getElementById('profesion_dropdown_list');
    const profesionOptions = profesionDropdown ? profesionDropdown.querySelectorAll('.search-select-option') : [];

    if (profesionInput) {
        profesionInput.addEventListener('focus', function() {
            profesionDropdown.style.display = 'block';
        });

        profesionInput.addEventListener('input', function() {
            const filter = profesionInput.value.toLowerCase();
            profesionDropdown.style.display = 'block';
            profesionOptions.forEach(option => {
                const text = option.textContent.toLowerCase();
                if (text.includes(filter)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });

        profesionOptions.forEach(option => {
            option.addEventListener('click', function() {
                profesionInput.value = option.textContent.trim();
                profesionHidden.value = option.getAttribute('data-value');
                profesionDropdown.style.display = 'none';
            });
        });

        document.addEventListener('click', function(e) {
            if (!profesionInput.contains(e.target) && !profesionDropdown.contains(e.target)) {
                profesionDropdown.style.display = 'none';
            }
        });
    }
});