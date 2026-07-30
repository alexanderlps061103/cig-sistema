/**
 * Lógica para la gestión visual de la nómina de docentes
 */

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalDocenteDetalle');
    const closeButtons = document.querySelectorAll('.close-modal');

    // Función para cerrar modal
    const closeModal = () => {
        modal.classList.remove('active');
    };

    // Asignar evento de cierre a los botones
    closeButtons.forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    // Cerrar si se hace clic fuera del contenido del modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Función global para ser llamada desde el atributo onclick del HTML
    window.verDetalleDocente = function(nombre, profesion, sesiones, experiencia) {
        // Rellenar campos del modal
        document.getElementById('detNombre').textContent = nombre;
        document.getElementById('detProfesion').textContent = profesion;
        document.getElementById('detSesiones').textContent = sesiones;
        document.getElementById('detExperiencia').textContent = experiencia || "El docente no ha registrado un resumen de experiencia en su currículum.";

        // Mostrar modal añadiendo la clase 'active' definida en tu CSS
        modal.classList.add('active');
    };
});