<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal del Estudiante - UNEY</title>
    <!-- Iconos de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos del Sistema -->
     <link rel="stylesheet" href="{{ asset('assets/css/styleBase.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/estudiante/dashboardEstudiante.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/menu/menu.css') }}">
</head>
<body>

    <!-- INCLUSIÓN MODULAR DE MENÚ, CAPA OSCURA Y BARRA SUPERIOR -->
    @include('layouts.sidebars.estudiante')

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content" id="main-content">
        
        <!-- Cabecera de Contenido -->
        <header class="main-header">
            <div class="header-welcome">
                <h1>Bienvenido al Portal Académico</h1>
                <p>Monitorea tu expediente, inscríbete en talleres y descarga tus certificados de asistencia.</p>
            </div>
            <div class="header-date">
                <i class="fa-solid fa-user-graduate"></i>
                <span>Estudiante Regular</span>
            </div>
        </header>

        <!-- Tarjetas de Métricas Rápidas (KPIs para Estudiantes) -->
        <section class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon icon-blue">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="kpi-info">
                    <span class="kpi-title">Inscripciones Activas</span>
                    <span class="kpi-value">3 Actividades</span>
                </div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-icon icon-green">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="kpi-info">
                    <span class="kpi-title">Horas de Formación</span>
                    <span class="kpi-value">12 Horas</span>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon icon-purple">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <div class="kpi-info">
                    <span class="kpi-title">Estado de Expediente</span>
                    <span class="kpi-value">Validado / Aprobado</span>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon icon-orange">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="kpi-info">
                    <span class="kpi-title">Encuestas Pendientes</span>
                    <span class="kpi-value">2 por Responder</span>
                </div>
            </div>
        </section>

        <!-- Área Central de Trabajo (Mis Inscripciones + Expediente / Trámites) -->
        <section class="dashboard-grid">
            
            <!-- Listado de Actividades Inscritas del Estudiante -->
            <div class="calendar-card">
                <div class="calendar-header">
                    <h2>Mis Próximas Actividades e Inscripciones</h2>
                </div>

                <!-- Tabla de Inscripciones Activas -->
                <div style="overflow-x: auto; margin-top: 15px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 1.2rem; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--color-border); color: var(--color-text-muted);">
                                <th style="padding: 12px 8px;">Actividad</th>
                                <th style="padding: 12px 8px;">Modalidad</th>
                                <th style="padding: 12px 8px;">Fecha / Hora</th>
                                <th style="padding: 12px 8px;">Lugar</th>
                                <th style="padding: 12px 8px; text-align: center;">Asistencia (QR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td style="padding: 16px 8px; font-weight: 600;">Taller: Introducción a Redes de Computadoras</td>
                                <td style="padding: 16px 8px;"><span class="event-tag badge-taller">Presencial</span></td>
                                <td style="padding: 16px 8px;">Viernes 15 Nov<br><small style="color: var(--color-text-muted);">10:00 AM</small></td>
                                <td style="padding: 16px 8px;">Laboratorio A</td>
                                <td style="padding: 16px 8px; text-align: center;">
                                    <button class="action-btn btn-primary-action show-qr-btn" data-id="1" style="padding: 6px 12px; font-size: 0.8rem; margin: 0 auto;">
                                        <i class="fa-solid fa-qrcode"></i> Mostrar QR
                                    </button>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td style="padding: 16px 8px; font-weight: 600;">Foro: Liderazgo y Planificación Institucional</td>
                                <td style="padding: 16px 8px;"><span class="event-tag badge-foro">Presencial</span></td>
                                <td style="padding: 16px 8px;">Miércoles 20 Nov<br><small style="color: var(--color-text-muted);">02:00 PM</small></td>
                                <td style="padding: 16px 8px;">Auditorio Principal</td>
                                <td style="padding: 16px 8px; text-align: center;">
                                    <button class="action-btn btn-primary-action show-qr-btn" data-id="2" style="padding: 6px 12px; font-size: 0.8rem; margin: 0 auto;">
                                        <i class="fa-solid fa-qrcode"></i> Mostrar QR
                                    </button>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td style="padding: 16px 8px; font-weight: 600;">Seminario: Herramientas de IA Aplicada</td>
                                <td style="padding: 16px 8px;"><span class="event-tag badge-charla">Semipresencial</span></td>
                                <td style="padding: 16px 8px;">Lunes 25 Nov<br><small style="color: var(--color-text-muted);">09:00 AM</small></td>
                                <td style="padding: 16px 8px;">Salón de Usos Múltiples</td>
                                <td style="padding: 16px 8px; text-align: center;">
                                    <button class="action-btn btn-primary-action show-qr-btn" data-id="3" style="padding: 6px 12px; font-size: 0.8rem; margin: 0 auto;">
                                        <i class="fa-solid fa-qrcode"></i> Mostrar QR
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Panel de Soporte y Expediente Escolar -->
            <aside class="filter-panel">
                <div class="panel-section">
                    <h3>Mi Expediente Digital</h3>
                    <p style="font-size: 1.15rem; color: var(--color-text-muted); margin-bottom: 15px;">
                        Control de documentos requeridos para participar en las actividades extracurriculares.
                    </p>
                    <ul style="list-style: none; font-size: 1.15rem; display: flex; flex-direction: column; gap: 10px;">
                        <li style="display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="fa-regular fa-file-pdf" style="color: var(--color-danger-text);"></i> Cédula de Identidad</span>
                            <span style="color: #10b981; font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Validado</span>
                        </li>
                        <li style="display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="fa-regular fa-file-pdf" style="color: var(--color-danger-text);"></i> Carta de Postulación</span>
                            <span style="color: #10b981; font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Validado</span>
                        </li>
                        <li style="display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="fa-regular fa-file-image" style="color: var(--color-text-active);"></i> Foto Carnet</span>
                            <span style="color: #f59e0b; font-weight: 600;"><i class="fa-solid fa-clock"></i> Pendiente</span>
                        </li>
                    </ul>
                </div>

                <div class="panel-section">
                    <h3>Trámites y Consultas</h3>
                    <button class="action-btn btn-primary-action">
                        <i class="fa-solid fa-file-signature"></i> Solicitar Carta Postulación
                    </button>
                    <button class="action-btn btn-secondary-action">
                        <i class="fa-solid fa-file-arrow-up"></i> Subir Soporte a Expediente
                    </button>
                </div>
            </aside>
        </section>
    </main>

    <!-- MODAL DE ASISTENCIA POR CÓDIGO QR -->
    <div class="modal-overlay" id="qr-modal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Verificación de Asistencia por QR</h3>
                <button class="modal-close" id="close-qr-modal-btn">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p style="font-size: 1.3rem; margin-bottom: 15px; color: var(--color-text-muted);">
                    Muestra este código al docente o planificador a cargo en la puerta del salón para validar tu entrada.
                </p>
                
                <!-- Simulación de código QR -->
                <div style="background-color: #f1f5f9; padding: 25px; border-radius: 12px; display: inline-block; margin-bottom: 15px; border: 1px dashed var(--color-border);">
                    <i class="fa-solid fa-qrcode" style="font-size: 15rem; color: var(--color-text-main);"></i>
                </div>
                
                <h4 id="qr-activity-title" style="font-size: 1.4rem; font-weight: 700; color: var(--color-text-main);">Cargando Actividad...</h4>
                <p id="qr-activity-location" style="font-size: 1.25rem; color: var(--color-text-muted); margin-top: 5px;">Salón...</p>
            </div>
            <div class="modal-footer" style="justify-content: center;">
                <button class="modal-btn btn-secondary" id="close-qr-footer-btn">Cerrar Código</button>
            </div>
        </div>
    </div>

    <!-- Scripts del Sistema -->
    <script src="{{ asset('assets/js/dashboardEstudiante.js') }}"></script>
    <script src="{{ asset('assets/js/menu.js') }}"></script>
</body>
</html>