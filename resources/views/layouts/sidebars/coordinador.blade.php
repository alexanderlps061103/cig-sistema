<!-- CONTENEDOR PRINCIPAL DEL MENU (Agregado para corregir el problema) -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <span class="logo-text">COORDINACIÓN</span>
        <button class="toggle-btn" id="toggle-btn" aria-label="Toggle Sidebar">
            <i class="fa-solid fa-bars" id="toggle-icon"></i>
        </button>
    </div>

    <div class="sidebar-content">
        {{-- SECCIÓN 1: DASHBOARD --}}
        <div class="menu-section">
            <span class="section-title">Principal</span>
            <ul class="menu-list">
                <li>
                    <a href="{{ route('coordinador.dashboard') }}">
                        <i class="fa-solid fa-chart-pie"></i> <span>Vista Global</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 2: PLANIFICACIÓN --}}
        <div class="menu-section">
            <span class="section-title">Planificación</span>
            <ul class="menu-list">
                <li class="has-submenu">
                    <a href="#" id="menu-planificacion">
                        <i class="fa-solid fa-calendar-days"></i> <span>Calendario y Lapsos</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <ul class="submenu" id="submenu-planificacion">
                        <li>
                            <a href="{{ route('coordinador.planificacion.index') }}">
                                <span>Planificaciones</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('coordinador.planificacion.trimestres.index') }}">
                                <span>Trimestres</span>
                            </a>
                        </li>
                        
                        {{-- Corregido: Feriados ahora apunta al controlador dinámico de CRUD --}}
                        <li><a href="{{ route('coordinador.entidades_crud.index', ['modulo' => 'feriado']) }}"><span>Dias Feriados</span></a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#" id="menu-estructura">
                        <i class="fa-solid fa-sitemap"></i> <span>Estructura Académica</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <ul class="submenu" id="submenu-estructura">
                        <li><a href="{{ route('coordinador.entidades_crud.index', ['modulo' => 'modalidad']) }}"><span>Modalidades</span></a></li>
                        <li><a href="{{ route('coordinador.entidades_crud.index', ['modulo' => 'denominacion']) }}"><span>Tipos de Actividad</span></a></li>
                        <li><a href="{{ route('coordinador.entidades_crud.index', ['modulo' => 'salon']) }}"><span>Salones</span></a></li>

                    </ul>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 3: OPERACIONES ACADÉMICAS --}}
        <div class="menu-section">
            <span class="section-title">Operaciones</span>
            <ul class="menu-list">
                <li class="has-submenu">
                    <a href="#" id="menu-actividades">
                        <i class="fa-solid fa-cubes"></i> <span>Gestión Actividades</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <ul class="submenu" id="submenu-actividades">
                        <li><a href="{{ route('coordinador.actividades.listado') }}"><span>Listado de actividades</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN 4: CONTROL DE USUARIOS --}}
        <div class="menu-section">
            <span class="section-title">Control de Usuarios</span>
            <ul class="menu-list">
                {{-- Bloque Actualizado: Activa la sección para redireccionar a Firmas y Sellos --}}
                <li class="has-submenu">
                    <a href="#" id="menu-certificaciones">
                        <i class="fa-solid fa-file-signature"></i> <span>Firmas y Sellos</span>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </a>
                    <ul class="submenu" id="submenu-certificaciones">
                        <li><a href="{{ route('coordinador.entidades_crud.index', ['modulo' => 'firma']) }}"><span>Firmas Autorizadas</span></a></li>
                        <li><a href="{{ route('coordinador.entidades_crud.index', ['modulo' => 'sello']) }}"><span>Sellos Institucionales</span></a></li>
                    </ul>
                </li> 
            </ul>
        </div>

        {{-- SECCIÓN 5: PERFIL PERSONAL --}}
        <div class="menu-section">
            <span class="section-title">Mi Perfil Personal</span>
            <ul class="menu-list">
                <li>
                    <a >
                        <i class="fa-solid fa-chalkboard-user"></i> <span>Mis Ponencias</span>
                    </a>
                </li>
                <li>
                    <a>
                        <i class="fa-solid fa-award"></i> <span>Mis Certificados</span>
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

<!-- CAPA OSCURECEDORA PARA DISPOSITIVOS MÓVILES (Agregada para corregir el JS) -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>