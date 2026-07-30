@extends('layouts.app')

@section('title', 'Inicio de Sesión')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth/login.css') }}">
@endpush

@section('content')
{{-- El contenedor login-container ahora se encarga de todo el centrado y el fondo --}}
<div class="login-container">
    <div class="login-card">
        <div class="login-row">
            <!-- Columna Logo -->
            <div class="login-logo-col">
                <img src="{{ asset('assets/images/logo_cig.svg') }}" alt="Logo SIACSACIG" class="login-logo-img">
            </div>

            <!-- Columna Formulario -->
            <div class="login-form-col">
                <div class="login-form-wrapper">
                    <div class="login-header">
                        <h1 class="login-title">¡Bienvenido!</h1>
                        <p style="text-align: center; color: var(--color-text-muted); margin-bottom: 2rem;">Inicia sesión en SIACSACIG</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success" style="background:#dcfce7; color:#166534; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger" style="background:#fee2e2; color:#b91c1c; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center; font-size:0.9rem;">
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    <form class="login-form" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group-custom">
                            <input type="email" class="form-input-custom"
                                name="email" id="email" value="{{ old('email') }}"
                                placeholder="Correo Electrónico" required autocomplete="email" autofocus>
                        </div>

                        <div class="form-group-custom">
                            <input type="password" class="form-input-custom"
                                name="password" id="password"
                                placeholder="Contraseña" required autocomplete="current-password">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">Recordarme</label>
                            </div>
                        </div>

                        <button type="submit" id="submit-login" class="btn-submit-custom">
                            INGRESAR
                        </button>
                    </form>

                    <div class="login-footer" style="margin-top: 2rem; text-align: center; border-top: 1px solid #eee; padding-top: 1.5rem;">
                        <a class="login-link" href="{{ route('register') }}">¿No tienes cuenta? Regístrate aquí</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/auth/login.js') }}"></script>
@endpush
