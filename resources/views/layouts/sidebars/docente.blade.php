<!-- BARRA SUPERIOR PARA DISPOSITIVOS MÓVILES 
     (Requerida por el JS y CSS para abrir el menú en resoluciones <= 1024px) -->
<div class="mobile-top-bar">
    <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Open Sidebar">
        <i class="fa-solid fa-bars"></i>
    </button>
    <span class="mobile-logo-text">DOCENTE</span>
</div>

<!-- CONTENEDOR PRINCIPAL DEL MENU -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <span class="logo-text">DOCENTE</span>
        <button class="toggle-btn" id="toggle-btn" aria-label="Toggle Sidebar">
            <i class="fa-solid fa-bars" id="toggle-icon"></i>
        </button>
    </div>

    <div class="sidebar-content">
        {{-- SECCIÓN 1: MI AGENDA --}}
        <div class="menu-section">
            <span class="section-title">Mi Agenda</span>
            <ul class="menu-list">
                <li>
                    <a href="{{ route('docente.dashboard') }}" class="active">
                        <i class="fa-solid fa-chart-pie"></i> <span>Vista Global</span>
                    </a>
                </li>
                <li class="has-submenu">
                    <a href="#" id="menu-planificacion">
                        <i class="fa-solid fa-chalkboard-user"></i> <span>Mis Sesiones</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <ul class="submenu" id="submenu-planificacion">
                        {{-- Se usan enlaces temporales '#' para evitar que falle la carga de la página --}}
                        <li><a href="#"><span>Sesiones Activas</span></a></li>
                        <li><a href="#"><span>Historial de Clases</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-calendar-check"></i> <span>Actividades Asignadas</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 2: HERRAMIENTAS --}}
        <div class="menu-section">
            <span class="section-title">Herramientas</span>
            <ul class="menu-list">
                <li>
                    <a href="#">
                        <i class="fa-solid fa-qrcode"></i> <span>Escanear Asistencia (QR)</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="has-badge">
                        <i class="fa-solid fa-star-half-stroke"></i> <span>Evaluaciones de Alumnos</span>
                        <span class="badge-count">5</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-file-signature"></i> <span>Mis Firmas y Sellos</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- PIE DEL MENÚ --}}
    <div class="sidebar-footer">
        <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-right-from-bracket"></i> <span>Cerrar Sesión</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

</aside>

<!-- CAPA OSCURECEDORA PARA DISPOSITIVOS MÓVILES -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>