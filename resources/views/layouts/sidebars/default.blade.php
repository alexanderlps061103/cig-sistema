<div class="sidebar-header">
    <span class="logo-text">SIACSACIG</span>
    <button id="toggle-btn" class="toggle-btn"><i class="fa-solid fa-bars"></i></button>
</div>
<div class="sidebar-content">
    <div class="menu-section">
        <span class="section-title">Panel</span>
        <ul class="menu-list">
            <li><a href="{{ url('/') }}"><i class="fa-solid fa-home"></i> <span>Inicio</span></a></li>
        </ul>
    </div>
</div>
<div class="sidebar-footer">
    <a href="{{ route('logout') }}" class="logout-btn"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Cerrar sesión</span>
    </a>
</div>
