document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalProcesarPasantia');
    const closeBtn = document.querySelector('.modal-close');
    const form = document.getElementById('formPasantia');
    const nombreEstudiante = document.getElementById('nombreEstudiante');

    window.abrirModalPasantia = (id, nombre) => {
        nombreEstudiante.innerText = nombre;
        // La ruta debe coincidir con la de aprobación de inducciones
        form.action = `/rector/pasantias/${id}/procesar`;
        modal.style.display = 'flex';
    };

    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => { if (e.target == modal) modal.style.display = 'none'; };
});
