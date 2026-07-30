<div class="sidebar-header">
    <span class="logo-text">ESTUDIANTE</span>
    <button class="toggle-btn" id="toggle-btn" aria-label="Toggle Sidebar">
        <i class="fa-solid fa-bars" id="toggle-icon"></i>
    </button>
</div>

<div class="sidebar-content">
    <div class="menu-section">
        <span class="section-title">Mi Espacio</span>
        <ul class="menu-list">
            <li>
                <a href="#" class="active">
                    <i class="fa-solid fa-house-user"></i>
                    <span>Inicio / Vista Global</span>
                </a>
            </li>
            <li class="has-submenu">
                <a href="#" id="menu-planificacion">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>Mis Inscripciones</span>
                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="submenu" id="submenu-planificacion">
                    <li><a href="#"><span>Inscripciones Activas</span></a></li>
                    <li><a href="#"><span>Historial de Talleres</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Mi Expediente</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="menu-section">
        <span class="section-title">Servicios</span>
        <ul class="menu-list">
            <li>
                <a href="#">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Explorar Actividades</span>
                </a>
            </li>
            <li>
                <a href="#" class="has-badge">
                    <i class="fa-solid fa-clipboard-question"></i>
                    <span>Encuestas</span>
                    <span class="badge-count">2</span>
                </a>
            </li>
            <li>
                <a href="#" class="has-badge">
                    <i class="fa-solid fa-award"></i>
                    <span>Mis Certificados</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="sidebar-footer">
    <a href="{{ route('logout') }}" class="logout-btn"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Cerrar sesión</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
