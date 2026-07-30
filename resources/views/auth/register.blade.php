@extends('layouts.app')

@section('title', 'Crear Cuenta')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth/register.css') }}">
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="register-card">
        <!-- Columna Logo -->
        <div class="register-logo-col">
            <img src="{{ asset('assets/images/logo_cig.svg') }}" alt="Logo SIACSACIG" class="register-logo-img">
        </div>

        <!-- Columna Formulario -->
        <div class="register-form-col">
            <div class="register-header">
                <h1 class="register-title">Crear Cuenta</h1>
            </div>

            <form id="register-form" action="{{ route('register') }}" method="POST" enctype="multipart/form-data" style="display: contents;">
                @csrf

                <!-- Contenedor con Scroll -->
                <div class="form-scroll-container">

                    {{-- Errores de Validación --}}
                    @if($errors->any())
                        <div class="alert alert-danger" style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:10px; margin-bottom:20px; font-size:0.85rem; border: 1px solid #fecaca;">
                            <strong>¡Ups! Algo salió mal:</strong><br>
                            <ul style="margin-top: 5px; margin-left: 15px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group-custom">
                        <label class="form-label-custom">¿Cómo deseas registrarte?</label>
                        <select name="tipo_registro" id="tipo_register_select" class="form-input-custom">
                            <option value="estudiante_externo" {{ old('tipo_registro') == 'estudiante_externo' ? 'selected' : '' }}>Público General</option>
                            <option value="estudiante_regular" {{ old('tipo_registro') == 'estudiante_regular' ? 'selected' : '' }}>Estudiante Regular (UNEY)</option>
                            <option value="aspirante_docente" {{ old('tipo_registro') == 'aspirante_docente' ? 'selected' : '' }}>Aspirante a Docente</option>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label class="form-label-custom">Cédula de Identidad</label>
                        <input type="text" class="form-input-custom" name="cedula" value="{{ old('cedula') }}" placeholder="Ej: 25123456" required>
                    </div>

                    <!-- Fila Unificada para Nombres y Apellidos -->
                    <div class="form-row">
                        <div>
                            <label class="form-label-custom">Nombres</label>
                            <input type="text" class="form-input-custom" name="nombres" value="{{ old('nombres') }}" placeholder="Ej: Juan Carlos" required>
                        </div>
                        <div>
                            <label class="form-label-custom">Apellidos</label>
                            <input type="text" class="form-input-custom" name="apellidos" value="{{ old('apellidos') }}" placeholder="Ej: Pérez Gómez" required>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label class="form-label-custom">Correo Electrónico</label>
                        <input type="email" class="form-input-custom" name="email" value="{{ old('email') }}" required placeholder="ejemplo@correo.com">
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label-custom">Género</label>
                            <select name="sexo" class="form-input-custom">
                                <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label-custom">Teléfono</label>
                            <input type="text" class="form-input-custom" name="telefono" value="{{ old('telefono') }}" placeholder="04121234567">
                        </div>
                    </div>

                    <!-- SECCIÓN DINÁMICA: ESTUDIANTE REGULAR (UNEY) -->
                    <div id="seccion_uney" style="display:none;">
                        <!-- Buscador dinámico de Carreras -->
                        <div class="form-group-custom search-select-wrapper">
                            <label class="form-label-custom">Carrera Universitaria</label>
                            <input type="text" id="carrera_search_input" class="form-input-custom" placeholder="Buscar y seleccionar carrera..." autocomplete="off">
                            <input type="hidden" name="carrera_id" id="carrera_id_hidden" value="{{ old('carrera_id') }}">
                            <div id="carrera_dropdown_list" class="search-select-dropdown" style="display:none;">
                                @foreach($carreras as $carrera)
                                    <div class="search-select-option" data-value="{{ $carrera->id }}">{{ $carrera->nombre }}</div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label-custom">Foto Selfie (De frente/cara)</label>
                            <input type="file" class="form-input-custom" name="foto_selfie" accept="image/*" style="padding: 8px 20px;">
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label-custom">Notas Certificadas (PDF)</label>
                            <input type="file" class="form-input-custom" name="notas_certificadas" accept=".pdf" style="padding: 8px 20px;">
                        </div>
                    </div>

                    <!-- SECCIÓN DINÁMICA: ASPIRANTE A DOCENTE -->
                    <div id="seccion_aspirante" style="display:none;">
                        <!-- Buscador dinámico de Profesiones -->
                        <div class="form-group-custom search-select-wrapper">
                            <label class="form-label-custom">Profesión / Título</label>
                            <input type="text" id="profesion_search_input" class="form-input-custom" placeholder="Buscar y seleccionar profesión..." autocomplete="off">
                            <input type="hidden" name="profesion_id" id="profesion_id_hidden" value="{{ old('profesion_id') }}">
                            <div id="profesion_dropdown_list" class="search-select-dropdown" style="display:none;">
                                @foreach($profesiones as $profesion)
                                    <div class="search-select-option" data-value="{{ $profesion->id }}">{{ $profesion->nombre }}</div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label-custom">Currículum Vitae (PDF)</label>
                            <input type="file" class="form-input-custom" name="cv_archivo" accept=".pdf" style="padding: 8px 20px;">
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label-custom">Foto de Cédula (Imagen de apoyo)</label>
                            <input type="file" class="form-input-custom" name="identificacion_png" accept="image/*" style="padding: 8px 20px;">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label-custom">Contraseña</label>
                            <input type="password" class="form-input-custom" name="password" required placeholder="Mín. 8 caracteres">
                        </div>
                        <div>
                            <label class="form-label-custom">Confirmar Contraseña</label>
                            <input type="password" class="form-input-custom" name="password_confirmation" required placeholder="Repita contraseña">
                        </div>
                    </div>
                </div>

                <div style="padding-top: 20px;">
                    <button type="submit" class="btn-submit-custom">REGISTRARSE</button>
                    <a href="{{ route('login') }}" class="login-link" style="display: block; text-align: center; margin-top: 15px; color: #4b5563; text-decoration: none; font-size: 0.9rem;">
                        ¿Ya tienes cuenta? <span style="color: #2563eb; font-weight: 600;">Inicia Sesión</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/auth/register.js') }}"></script>
@endpush