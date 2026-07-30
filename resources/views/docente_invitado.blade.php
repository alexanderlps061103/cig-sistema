@extends('layouts.app')

@section('title', 'Registrar Docente Invitado')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/docente_invitado.css') }}">
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="register-card">
        <div class="register-logo-col">
            <img src="{{ asset('assets/images/logo_cig.svg') }}" alt="Logo SIACSACIG" class="register-logo-img">
        </div>

        <div class="register-form-col">
            <div class="register-header">
                <h1 class="register-title">Docente Invitado</h1>
                <p style="color: var(--color-text-muted); text-align: center; margin-bottom: 20px;">Registro de personal externo de apoyo</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success" style="background:#dcfce7; color:#166534; padding:12px; border-radius:8px; margin-bottom:15px; text-align:center;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="background:#fee2e2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:15px; font-size:0.85rem;">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form id="guest-teacher-form" action="{{ route('docentes.invitado.store') }}" method="POST">
                @csrf
                <div class="form-scroll-container">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Cédula de Identidad</label>
                        <input type="text" class="form-input-custom" name="cedula" value="{{ old('cedula') }}" placeholder="Ej: 12345678" required>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label-custom">Nombres</label>
                            <input type="text" class="form-input-custom" name="nombres" value="{{ old('nombres') }}" placeholder="Nombres" required>
                        </div>
                        <div>
                            <label class="form-label-custom">Apellidos</label>
                            <input type="text" class="form-input-custom" name="apellidos" value="{{ old('apellidos') }}" placeholder="Apellidos" required>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label class="form-label-custom">Correo Electrónico</label>
                        <input type="email" class="form-input-custom" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
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

                    <!-- Buscador dinámico de Profesiones -->
                    <div class="form-group-custom search-select-wrapper">
                        <label class="form-label-custom">Profesión / Especialidad</label>
                        <input type="text" id="profesion_guest_input" class="form-input-custom" placeholder="Buscar y seleccionar profesión..." autocomplete="off">
                        <input type="hidden" name="profesion_id" id="profesion_guest_hidden" value="{{ old('profesion_id') }}">
                        <div id="profesion_guest_dropdown" class="search-select-dropdown" style="display:none;">
                            @foreach($profesiones as $profesion)
                                <div class="search-select-option" data-value="{{ $profesion->id }}">{{ $profesion->nombre }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="padding-top: 20px;">
                    <button type="submit" class="btn-submit-custom">REGISTRAR INVITADO</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- CORREGIDO: Enlace al archivo JS reorganizado en la carpeta base -->
    <script src="{{ asset('assets/js/docente_invitado.js') }}"></script>
@endpush