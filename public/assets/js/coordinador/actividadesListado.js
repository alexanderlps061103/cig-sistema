document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('filter-search');
    const statusSelect = document.getElementById('filter-status');
    const noResultsRow = document.getElementById('no-results-row');

    if (!searchInput || !statusSelect) return;

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusSelect.value.toLowerCase();
        let totalVisibleActivities = 0;

        const accordionItems = document.querySelectorAll('.accordion-item');

        accordionItems.forEach(item => {
            const rows = item.querySelectorAll('.data-row');
            let visibleRowsInTrimester = 0;

            rows.forEach(row => {
                const nombre = row.getAttribute('data-nombre') || '';
                const descripcion = row.getAttribute('data-descripcion') || '';
                const estado = row.getAttribute('data-estado') || '';

                const matchesSearch = nombre.includes(query) || descripcion.includes(query);
                const matchesStatus = selectedStatus === '' || estado === selectedStatus;

                if (matchesSearch && matchesStatus) {
                    row.style.display = 'flex';
                    visibleRowsInTrimester++;
                    totalVisibleActivities++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleRowsInTrimester > 0) {
                item.style.display = 'block';
                
                const content = item.querySelector('.accordion-content');
                if (content && content.classList.contains('active')) {
                    content.style.maxHeight = content.scrollHeight + "px";
                }
            } else {
                item.style.display = 'none';
            }
        });

        if (totalVisibleActivities === 0) {
            if (noResultsRow) noResultsRow.style.display = 'flex';
        } else {
            if (noResultsRow) noResultsRow.style.display = 'none';
        }
    }

    searchInput.addEventListener('input', applyFilters);
    statusSelect.addEventListener('change', applyFilters);
});