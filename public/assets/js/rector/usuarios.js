// 1. PRIMERO: Definir las variables globales (Sin esto nada funciona)
const userModal = document.getElementById('user-modal');
const statusModal = document.getElementById('status-modal');
const userForm = document.getElementById('user-form');
const methodContainer = document.getElementById('method-container');

// 2. Función para abrir el modal de CREAR
function openCreateModal() {
    if (!userForm) return; // Seguridad
    document.getElementById('modal-title').innerText = "Registrar Nuevo Usuario";
    userForm.action = "/rector/usuarios"; // Ruta de store
    methodContainer.innerHTML = ""; // Sin método PUT
    userForm.reset(); // Limpiar campos

    document.getElementById('form-cedula').readOnly = false;
    document.getElementById('form-password').placeholder = "Mínimo 8 caracteres";

    handleRoleChange(); // Ajustar secciones de perfil
    userModal.classList.add('active');
}

// 3. Función para abrir el modal de EDITAR
function openEditModal(persona) {
    if (!userForm) return;
    document.getElementById('modal-title').innerText = "Editar Perfil de Usuario";
    userForm.action = `/rector/usuarios/${persona.id}`;
    methodContainer.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

    // Llenar datos personales
    document.getElementById('form-cedula').value = persona.cedula || '';
    document.getElementById('form-cedula').readOnly = true;
    document.getElementById('form-nombres').value = persona.nombres || '';
    document.getElementById('form-apellidos').value = persona.apellidos || '';
    document.getElementById('form-email').value = persona.usuario ? persona.usuario.email : '';
    document.getElementById('form-telefono').value = persona.telefono || '';
    document.getElementById('form-sexo').value = persona.sexo || 'M';
    document.getElementById('form-password').placeholder = "Solo si desea cambiarla";

    // Asignar Rol
    if(persona.roles && persona.roles.length > 0) {
        document.getElementById('form-rol-id').value = persona.roles[0].id;
    }

    // --- CARGAR DATOS DE PROFESIÓN Y CARGO ---
    if(persona.estudiante) {
        document.getElementById('form-carrera-id').value = persona.estudiante.carrera_id;
    }

    if(persona.docentes) {
        document.getElementById('form-profesion-id').value = persona.docentes.profesion_id;
    }

    if(persona.empleado) {
        document.getElementById('form-cargo-id').value = persona.empleado.cargo_id;
    }

    handleRoleChange();
    userModal.classList.add('active');
}

// 4. Lógica de mostrar/ocultar campos según rol
function handleRoleChange() {
    const select = document.getElementById('form-rol-id');
    if (!select) return;

    const selectedOption = select.options[select.selectedIndex];
    const rolNombre = selectedOption ? selectedOption.getAttribute('data-nombre') : '';

    const secEstudiante = document.getElementById('section-estudiante');
    const secEmpleado = document.getElementById('section-empleado');

    if (secEstudiante) {
        secEstudiante.style.display = (rolNombre === 'estudiante') ? 'block' : 'none';
    }

    if (secEmpleado) {
        secEmpleado.style.display = (['docente', 'coordinador', 'rector'].includes(rolNombre)) ? 'block' : 'none';
    }
}

// 5. Modal de cambio de estado (Habilitar/Deshabilitar)
function openStatusModal(id, name) {
    const statusForm = document.getElementById('status-form');
    document.getElementById('status-user-name').innerText = name;
    statusForm.action = `/rector/usuarios/${id}/toggle`;
    statusModal.classList.add('active');
}

// 6. Funciones para cerrar
function closeUserModal() {
    userModal.classList.remove('active');
}

function closeStatusModal() {
    statusModal.classList.remove('active');
}

// 7. Cerrar al hacer clic fuera del modal
window.onclick = function(event) {
    if (event.target == userModal) closeUserModal();
    if (event.target == statusModal) closeStatusModal();
}
