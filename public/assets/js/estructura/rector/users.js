// public/assets/js/rector/users.js
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[action*="/usuarios/"][method="POST"]').forEach(form => {
    form.addEventListener('submit', e => {
      if (! confirm('¿Estás seguro?')) e.preventDefault();
    });
  });
});
