document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalProcesar');
    const formProcesar = document.getElementById('formProcesarSolicitud');
    const nombrePostulante = document.getElementById('nombrePostulante');
    const profesionPostulante = document.getElementById('profesionPostulante');
    const mensajePostulante = document.getElementById('mensajePostulante');

    // Función global para abrir el modal desde los botones del Blade
    window.abrirModalProcesar = (id, nombre, profesion, mensaje) => {
        if (!modal || !formProcesar) return;

        nombrePostulante.innerText = nombre;
        profesionPostulante.innerText = profesion || 'No registrada';
        mensajePostulante.innerText = mensaje || 'Sin mensaje adicional.';

        // Asignación dinámica de la ruta
        formProcesar.action = `/rector/solicitudes/${id}/procesar`;

        // Mostrar modal (soporta tanto display flex como la clase active)
        modal.classList.add('active');
        modal.style.display = 'flex';
    };

    // Función global para cerrar el modal
    window.cerrarModal = () => {
        if (!modal) return;
        modal.classList.remove('active');
        modal.style.display = 'none';
    };

    // Cierre al hacer clic fuera del contenido
    window.onclick = (e) => {
        if (e.target === modal) {
            window.cerrarModal();
        }
    };
});
