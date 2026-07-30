<!-- BARRA SUPERIOR PARA DISPOSITIVOS MÓVILES 
     (Requerida por el JS y CSS para abrir el menú en resoluciones <= 1024px) -->
<div class="mobile-top-bar">
    <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Open Sidebar">
        <i class="fa-solid fa-bars"></i>
    </button>
    <span class="mobile-logo-text">SIACSACIG</span>
</div>

<!-- CONTENEDOR PRINCIPAL DEL MENU -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <span class="logo-text">SIACSACIG</span>
        <button id="toggle-btn" class="toggle-btn" aria-label="Toggle Sidebar">
            <i class="fa-solid fa-bars" id="toggle-icon"></i>
        </button>
    </div>

    <div class="sidebar-content">
        {{-- SECCIÓN 1: PRINCIPAL --}}
        <div class="menu-section">
            <span class="section-title">Principal</span>
            <ul class="menu-list">
                <li>
                    <a href="{{ route('rector.dashboard') }}">
                        <i class="fa-solid fa-chart-line"></i> <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 2: Gestionar Usuarios --}}
        <div class="menu-section">
            <span class="section-title">Gestionar Usuarios</span>
            <ul class="menu-list">
                <li>
                    <a href="{{ route('rector.usuarios.index') }}">
                        <i class="fa-solid fa-users"></i> <span>Control de Usuarios</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rector.solicitudes.index') }}">
                        <i class="fa-solid fa-file-circle-check"></i> <span>Solicitudes de Empleo</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rector.docentes.index') }}">
                        <i class="fa-solid fa-chalkboard-user"></i> <span>Gestionar docentes</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 3: ESTRUCTURA INSTITUCIONAL --}}
        <div class="menu-section">
            <span class="section-title">Estructura Institucional</span>
            <ul class="menu-list">
                <li>
                    <a href="{{ route('rector.cargos.index') }}">
                        <i class="fa-solid fa-briefcase"></i> <span>Gestionar Cargos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rector.profesiones.index') }}">
                        <i class="fa-solid fa-user-tie"></i> <span>Gestionar Profesiones</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rector.carreras.index') }}">
                        <i class="fa-solid fa-school"></i> <span>Carreras Universitarias</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rector.tipo_estudiantes.index') }}">
                        <i class="fa-solid fa-id-card-clip"></i> <span>Tipos de Estudiante</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 4: ACADÉMICO Y PASANTÍAS --}}
        <div class="menu-section">
            <span class="section-title">Académico y Pasantías</span>
            <ul class="menu-list">
                <li>
                    <a href="{{ route('rector.pasantias.index') }}">
                        <i class="fa-solid fa-graduation-cap"></i> <span>Inducción Pasantías</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rector.reportes.actividades') }}">
                        <i class="fa-solid fa-calendar-check"></i> <span>Verificar Actividades</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 5: PERFIL DOCENTE (PARA EL RECTOR) --}}
        <div class="menu-section">
            <span class="section-title">Mi Perfil (Docente)</span>
            <ul class="menu-list">
                <li>
                    <a href="{{ route('rector.mis_sesiones') }}">
                        <i class="fa-solid fa-chalkboard"></i> <span>Mis Sesiones</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rector.mis_certificados') }}">
                        <i class="fa-solid fa-certificate"></i> <span>Mis Certificados</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 6: REPORTES --}}
        <div class="menu-section">
            <span class="section-title">Reportes y Exportación</span>
            <ul class="menu-list">
                <li>
                    <a href="{{ route('rector.exportar.actividades.pdf') }}">
                        <i class="fa-solid fa-file-pdf"></i> <span>Reporte General PDF</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rector.reportes.empleo') }}">
                        <i class="fa-solid fa-chart-pie"></i> <span>Estadísticas Empleo</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- PIE DEL SIDEBAR --}}
    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="logout-btn"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Cerrar sesión</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

</aside>

<!-- CAPA OSCURECEDORA PARA DISPOSITIVOS MÓVILES -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>