document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('filter-search-input');
    const accordionItems = document.querySelectorAll('.accordion-item');
    const noResultsBox = document.getElementById('no-results-box');

    // 1. Buscador dinámico de Actividades
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let matches = 0;

            accordionItems.forEach(item => {
                const nombre = item.getAttribute('data-nombre').toLowerCase();
                if (nombre.includes(query)) {
                    item.style.display = 'block';
                    matches++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (matches === 0 && query !== '') {
                noResultsBox.style.display = 'block';
            } else {
                noResultsBox.style.display = 'none';
            }
        });
    }
});

// 2. Función interactiva para abrir y colapsar acordeones
function toggleAccordion(header) {
    const item = header.parentElement;
    const content = item.querySelector('.accordion-content');
    const isActive = item.classList.contains('active');

    // Cerrar otros acordeones abiertos para mantener el orden visual
    document.querySelectorAll('.accordion-item').forEach(otherItem => {
        if (otherItem !== item && otherItem.classList.contains('active')) {
            otherItem.classList.remove('active');
            otherItem.querySelector('.accordion-content').style.maxHeight = null;
        }
    });

    // Alternar el estado del acordeón actual
    if (isActive) {
        item.classList.remove('active');
        content.style.maxHeight = null;
    } else {
        item.classList.add('active');
        content.style.maxHeight = content.scrollHeight + "px";
    }
}