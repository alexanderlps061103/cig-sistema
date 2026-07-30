document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submit-login');

    // Evitar envío si faltan campos (HTML5 ya lo hace, pero añadimos un efecto visual)
    loginForm.addEventListener('submit', function(e) {
        let error = false;
        // Limpiar mensajes anteriores
        document.querySelectorAll('.validation-message').forEach(el => el.remove());

        if (!emailInput.value.trim()) {
            showValidationMessage(emailInput, 'El correo electrónico es obligatorio.');
            error = true;
        } else if (!isValidEmail(emailInput.value)) {
            showValidationMessage(emailInput, 'Formato de correo no válido.');
            error = true;
        }

        if (!passwordInput.value.trim()) {
            showValidationMessage(passwordInput, 'La contraseña es obligatoria.');
            error = true;
        }

        if (error) {
            e.preventDefault();
            // Deshabilitar botón momentáneamente para evitar doble envío
            submitBtn.disabled = false;
        } else {
            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.textContent = 'Ingresando…';
        }
    });

    function showValidationMessage(inputElement, message) {
        const msg = document.createElement('div');
        msg.className = 'validation-message text-danger small mt-1';
        msg.textContent = message;
        inputElement.parentNode.appendChild(msg);
        inputElement.classList.add('is-invalid');
        inputElement.addEventListener('input', function() {
            msg.remove();
            inputElement.classList.remove('is-invalid');
        }, { once: true });
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
});
