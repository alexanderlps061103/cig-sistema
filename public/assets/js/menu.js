document.addEventListener('DOMContentLoaded', () => {
    // === COMPONENTES DE MENÚ Y SIDEBAR ===
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    
    // Seleccionamos dinámicamente todos los elementos que contienen submenús
    const submenuItems = document.querySelectorAll('.has-submenu');

    // === GESTIÓN DE APERTURA/CIERRE DEL MENÚ (MÓVIL Y ESCRITORIO) ===
    
    function closeMobileMenu() {
        if (sidebar) sidebar.classList.remove('mobile-open');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
    }

    // Botón de Hamburguesa en Móvil (Abre el Menú)
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.add('mobile-open');
            sidebarOverlay.classList.add('active');
        });
    }

    // Botón de colapso en Escritorio / Cierre en Móvil
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                closeMobileMenu();
            } else {
                sidebar.classList.toggle('collapsed');
                
                // Si se colapsa, cerramos todos los submenús activos dinámicamente
                if (sidebar.classList.contains('collapsed')) {
                    submenuItems.forEach(item => {
                        item.classList.remove('open');
                        const submenu = item.querySelector('.submenu');
                        if (submenu) {
                            submenu.classList.remove('open');
                            submenu.style.maxHeight = null; // CORRECCIÓN: Limpia la altura para evitar que queden desbordados
                        }
                    });
                }
            }
        });
    }

    // Al hacer clic sobre la capa oscura (overlay)
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            closeMobileMenu();
        });
    }

    // Cierra el menú móvil si la pantalla se agranda más allá de 1024px
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            closeMobileMenu();
        }
    });

    // === GESTIÓN DE SUBMENÚS DESPLEGABLES ===
    // Inicializamos todos los submenús dinámicamente
    submenuItems.forEach(item => {
        const triggerBtn = item.querySelector('a'); // El enlace que activa el menú
        const submenuEl = item.querySelector('.submenu'); // El submenú interno (ul)
        
        if (triggerBtn && submenuEl) {
            triggerBtn.addEventListener('click', (e) => {
                e.preventDefault(); 
                
                if (window.innerWidth > 1024 && sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                }
                
                // CORRECCIÓN: Alternamos las clases correspondientes y calculamos la altura usando el scrollHeight del submenú
                const isOpen = item.classList.contains('open');
                
                if (isOpen) {
                    item.classList.remove('open');
                    submenuEl.classList.remove('open');
                    submenuEl.style.maxHeight = null; // Cierra limpiando el alto en línea
                } else {
                    item.classList.add('open');
                    submenuEl.classList.add('open');
                    submenuEl.style.maxHeight = submenuEl.scrollHeight + 'px'; // Abre adaptando la altura al contenido real
                }
            });
        }
    });
});