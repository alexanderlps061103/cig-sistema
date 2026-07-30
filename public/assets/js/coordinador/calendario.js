/**
 * ARCHIVO: public/assets/js/coordinador/calendario.js
 * Responsabilidad: Generar dinámicamente la grilla del calendario basándose en el 
 * mes/año seleccionado, inyectar las actividades y feriados, y gestionar la navegación.
 */

document.addEventListener('DOMContentLoaded', () => {
    const gridContainer = document.getElementById('calendar-grid-container');
    const monthYearTitle = document.getElementById('month-year-title');
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');

    if (!gridContainer || !monthYearTitle) {
        console.warn("[Calendario] No se encontraron los contenedores del calendario en el DOM.");
        return;
    }

    // Inicializar el calendario en el mes actual
    let currentDate = new Date();

    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];

    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();

        // 1. Actualizar el título
        monthYearTitle.innerText = `${meses[month]} ${year}`;

        // Limpiar el contenido anterior de la grilla
        gridContainer.innerHTML = '';

        // 2. Renderizar los nombres de los días de la semana
        const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        diasSemana.forEach(dia => {
            const dayNameDiv = document.createElement('div');
            dayNameDiv.className = 'day-name';
            dayNameDiv.innerText = dia;
            gridContainer.appendChild(dayNameDiv);
        });

        // 3. Obtener el índice del primer día y el total de días del mes
        const firstDayIndex = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();
        const prevLastDate = new Date(year, month, 0).getDate();

        // 4. Renderizar días sobrantes del mes anterior
        for (let x = firstDayIndex; x > 0; x--) {
            const cell = document.createElement('div');
            cell.className = 'day-cell prev-month';
            cell.innerHTML = `<span>${prevLastDate - x + 1}</span>`;
            gridContainer.appendChild(cell);
        }

        // 5. Renderizar días del mes actual e inyectar sus respectivos eventos
        for (let day = 1; day <= lastDate; day++) {
            const cell = document.createElement('div');
            cell.className = 'day-cell';
            
            // Construir formato de fecha YYYY-MM-DD
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            cell.innerHTML = `<span>${day}</span>`;

            // CORREGIDO: Corrección de error de sintaxis "window.ACTIVsplitATA"
            if (window.ACTIVIDADES_DATA && Array.isArray(window.ACTIVIDADES_DATA)) {
                const eventsOfDay = window.ACTIVIDADES_DATA.filter(event => event.fecha === dateStr);

                eventsOfDay.forEach(event => {
                    const eventTag = document.createElement('div');
                    
                    if (event.type === 'feriado') {
                        eventTag.className = 'event-tag badge-feriado';
                        eventTag.innerHTML = `<i class="fa-solid fa-umbrella-beach"></i> ${event.nombre}`;
                    } else {
                        let badgeColorClass = 'badge-taller'; // color estándar (azul)
                        const tipoStr = (event.tipo || '').toLowerCase();
                        if (tipoStr.includes('foro')) badgeColorClass = 'badge-foro'; // verde
                        if (tipoStr.includes('charla')) badgeColorClass = 'badge-charla'; // morado
                        
                        eventTag.className = `event-tag ${badgeColorClass}`;
                        eventTag.innerText = event.nombre;
                    }

                    eventTag.setAttribute('data-id', event.id);
                    eventTag.setAttribute('data-type', event.type);
                    cell.appendChild(eventTag);
                });
            }

            gridContainer.appendChild(cell);
        }
    }

    // Delegación de clics globales para abrir las ventanas emergentes de detalles
    document.addEventListener('click', (e) => {
        const tag = e.target.closest('.event-tag');
        if (tag) {
            e.stopPropagation();
            const id = tag.getAttribute('data-id');
            const type = tag.getAttribute('data-type') || 'actividad';

            let foundData = null;

            if (window.ACTIVIDADES_DATA && Array.isArray(window.ACTIVIDADES_DATA)) {
                foundData = window.ACTIVIDADES_DATA.find(item => item.id == id && item.type === type);
            }

            // Soporte Fallback: Datos locales de demostración
            if (!foundData) {
                const mockTestData = {
                    "1": { 
                        id: 1, 
                        type: "actividad", 
                        nombre: "Taller: Introducción a Redes de Computadoras", 
                        planificacion_nombre: "Planificación 2026", 
                        trimestre_nombre: "2026 - Trimestre 1", 
                        modalidad: "Presencial", 
                        tipo: "Taller", 
                        aula: "Laboratorio A (Planta Alta)", 
                        fecha: "2026-07-03", 
                        horario: "10:00 AM a 12:00 PM",
                        sesiones_conteo: 3
                    },
                    "2": { 
                        id: 2, 
                        type: "actividad", 
                        nombre: "Foro: Liderazgo y Planificación Institucional", 
                        planificacion_nombre: "Planificación 2026", 
                        trimestre_nombre: "2026 - Trimestre 2", 
                        modalidad: "Virtual", 
                        tipo: "Foro", 
                        aula: "Auditorio Principal (Planta Baja)", 
                        fecha: "2026-07-10", 
                        horario: "02:00 PM a 04:00 PM",
                        sesiones_conteo: 5
                    },
                    "3": { 
                        id: 3, 
                        type: "actividad", 
                        nombre: "Charla: Inteligencia Artificial en la Educación", 
                        planificacion_nombre: "Planificación 2026", 
                        trimestre_nombre: "2026 - Trimestre 3", 
                        modalidad: "Semipresencial", 
                        tipo: "Charla", 
                        aula: "Aula de Usos Múltiples 3", 
                        fecha: "2026-07-15", 
                        horario: "09:00 AM a 11:00 AM",
                        sesiones_conteo: 1
                    },
                    "101": { 
                        id: 101, 
                        type: "feriado", 
                        nombre: "Feriado: Día de la Resistencia Indígena", 
                        fecha: "2026-07-12", 
                        descripcion: "Festividad nacional declarada de asueto oficial no laborable." 
                    }
                };
                foundData = mockTestData[id];
            }

            if (foundData) {
                if (typeof window.mostrarDetalleActividadDesdeCalendario === 'function') {
                    window.mostrarDetalleActividadDesdeCalendario(foundData);
                } else {
                    console.error("[Calendario] Error: La función global 'window.mostrarDetalleActividadDesdeCalendario' no está disponible.");
                }
            }
        }
    });

    // Navegación - Mes Anterior
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });
    }

    // Navegación - Mes Siguiente
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });
    }

    // Renderizar al inicializar
    renderCalendar(currentDate);
});