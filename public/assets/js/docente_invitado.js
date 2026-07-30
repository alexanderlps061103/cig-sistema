document.addEventListener('DOMContentLoaded', function() {
    const inputSearch = document.getElementById('profesion_guest_input');
    const inputHidden = document.getElementById('profesion_guest_hidden');
    const dropdownList = document.getElementById('profesion_guest_dropdown');
    const options = dropdownList ? dropdownList.querySelectorAll('.search-select-option') : [];

    if (inputSearch) {
        inputSearch.addEventListener('focus', function() {
            dropdownList.style.display = 'block';
        });

        inputSearch.addEventListener('input', function() {
            const filter = inputSearch.value.toLowerCase();
            dropdownList.style.display = 'block';
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                if (text.includes(filter)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });

        options.forEach(option => {
            option.addEventListener('click', function() {
                inputSearch.value = option.textContent.trim();
                inputHidden.value = option.getAttribute('data-value');
                dropdownList.style.display = 'none';
            });
        });

        document.addEventListener('click', function(e) {
            if (!inputSearch.contains(e.target) && !dropdownList.contains(e.target)) {
                dropdownList.style.display = 'none';
            }
        });
    }
});