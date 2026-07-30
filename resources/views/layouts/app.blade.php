<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos comunes (variables, base, etc.) -->
    <link rel="stylesheet" href="{{ asset('assets/css/styleBase.css') }}">
    <!-- Menú lateral + barra superior -->
    <link rel="stylesheet" href="{{ asset('assets/css/menu/menu.css') }}">
    @stack('styles')
</head>
<body class="@auth has-sidebar @endauth">
@auth
    @php
        $usuario = Auth::user();
        $persona = $usuario->persona;
        $roleActivo = session('role_activo') ?? ($persona && $persona->roles->count() ? $persona->roles->first()->nombre : 'publico');
    @endphp

    <!-- Barra lateral dinámica según rol -->
    <aside>
        @includeFirst([
            "layouts.sidebars.{$roleActivo}",
            'layouts.sidebars.default'
        ])
    </aside>

    <!-- Capa oscura para móvil -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>

    <!-- Barra superior móvil -->
    <div class="mobile-top-bar">
        <button id="mobile-menu-btn" class="mobile-menu-btn" aria-label="Abrir menú">
            <i class="fa-solid fa-bars"></i>
        </button>
        <span class="mobile-logo-text">SIACSACIG</span>
    </div>

    <!-- Contenido principal (wrapper) -->
    <div class="main-wrapper">
        <!-- Barra superior de escritorio -->
        <header class="top-header">
            <div class="top-header-left">
                <span class="welcome-text">Bienvenido, {{ $persona->nombres }} {{ $persona->apellidos }}</span>
            </div>
            <div class="top-header-right">
                <div class="user-info">
                    <span class="role-badge">{{ ucfirst($roleActivo) }}</span>
                    <span class="user-name">{{ $persona->nombres }} {{ $persona->apellidos }}</span>
                </div>
                
            </div>
        </header>

        <!-- Contenido de la página -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
 @else
        {{-- Quitamos wrappers viejos y dejamos solo este --}}
        <div class="auth-wrapper">
            @yield('content')
        </div>
    @endauth

    <script src="{{ asset('assets/js/menu.js') }}"></script>

    @stack('scripts')
    
</body>
</html>
