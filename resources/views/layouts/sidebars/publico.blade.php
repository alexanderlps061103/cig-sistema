<!-- CONTENEDOR PRINCIPAL DEL MENU (Replicando la estructura del coordinador) -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <span class="logo-text">SIACSACIG</span>
        <button class="toggle-btn" id="toggle-btn" aria-label="Toggle Sidebar">
            <i class="fa-solid fa-bars" id="toggle-icon"></i>
        </button>
    </div>

    <div class="sidebar-content">
        {{-- SECCIÓN 1: PRINCIPAL --}}
        <div class="menu-section">
            <span class="section-title">Principal</span>
            <ul class="menu-list">
                <!-- Redirecciona al dashboard interactivo del público -->
                <li class="{{ request()->routeIs('publico.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('publico.dashboard') }}">
                        <i class="fa-solid fa-home"></i> <span>Inicio</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 2: ACTIVIDADES --}}
        <div class="menu-section">
            <span class="section-title">Actividades</span>
            <ul class="menu-list">
                <li class="{{ request()->routeIs('publico.actividades.index') ? 'active' : '' }}">
                    <a href="{{ route('publico.actividades.index') }}">
                        <i class="fa-solid fa-calendar"></i> <span>Próximas Actividades</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('publico.certificados') ? 'active' : '' }}">
                    <a href="{{ route('publico.certificados') }}">
                        <i class="fa-solid fa-certificate"></i> <span>Mis Certificados</span>
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

<!-- CAPA OSCURECEDORA PARA DISPOSITIVOS MÓVILES (Replicada del coordinador para corregir el comportamiento táctil/móvil) -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>