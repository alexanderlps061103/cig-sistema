@extends('layouts.app')

@section('title', 'Bienvenido a SIACSACIG')

@section('content')
<div class="container py-5">
    <div class="text-center">
        <img src="{{ asset('assets/images/logo_cig.svg') }}" alt="Logo" style="max-height: 160px;" class="mb-4">
        <h1 class="display-4 fw-bold">Plataforma de Gestión CIG</h1>
        <p class="lead text-muted">Centro de Investigaciones Gastronómicas – UNEY</p>
    </div>

    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fa-solid fa-chalkboard-user fa-3x mb-3" style="color: var(--color-brand-blue);"></i>
                    <h5>Cursos y Actividades</h5>
                    <p>Explora nuestra oferta académica y participa en actividades formativas.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fa-solid fa-certificate fa-3x mb-3" style="color: var(--color-brand-blue);"></i>
                    <h5>Certificados Digitales</h5>
                    <p>Obtén tus certificados al completar las actividades.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fa-solid fa-users fa-3x mb-3" style="color: var(--color-brand-blue);"></i>
                    <h5>Comunidad</h5>
                    <p>Conéctate con docentes, estudiantes y profesionales del área.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        @guest
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 me-3">Iniciar Sesión</a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5">Registrarse</a>
        @endguest

        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5">Ir al Panel</a>
        @endauth
    </div>
</div>
@endsection
