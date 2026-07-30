<!-- Bloque del Calendario -->
<div class="calendar-card">
    <div class="calendar-header">
        <h2>Calendario de Actividades</h2>
        <div class="calendar-navigation">
            <button class="cal-nav-btn" id="prev-month" aria-label="Mes Anterior">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <span class="current-month-year" id="month-year-title">Cargando...</span>
            <button class="cal-nav-btn" id="next-month" aria-label="Mes Siguiente">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
    
    <!-- Grilla del Calendario (Se genera dinámicamente por JS) -->
    <div class="calendar-grid" id="calendar-grid-container">
        <!-- Los días de la semana y las celdas de números se inyectarán aquí -->
         <div class="calendar-grid">
            <div class="day-name">Dom</div>
            <div class="day-name">Lun</div>
            <div class="day-name">Mar</div>
            <div class="day-name">Mié</div>
            <div class="day-name">Jue</div>
            <div class="day-name">Vie</div>
            <div class="day-name">Sáb</div>

            <!-- Fila del Calendario (Días del mes anterior) -->
            <div class="day-cell prev-month"><span>29</span></div>
            <div class="day-cell prev-month"><span>30</span></div>
            
            <!-- Días del mes activo -->
            <div class="day-cell"><span>1</span></div>
            <div class="day-cell"><span>2</span></div>
            <div class="day-cell">
                <span>3</span>
                <div class="event-tag badge-taller" data-id="1">Taller: Redes</div>
            </div>
            <div class="day-cell"><span>4</span></div>
            <div class="day-cell"><span>5</span></div>
            <div class="day-cell"><span>6</span></div>
            <div class="day-cell"><span>7</span></div>
            <div class="day-cell"><span>8</span></div>
            <div class="day-cell"><span>9</span></div>
            <div class="day-cell">
                <span>10</span>
                <div class="event-tag badge-foro" data-id="2">Foro: Liderazgo</div>
            </div>
            <div class="day-cell"><span>11</span></div>
            
            <!-- EJEMPLO: Día 12 de Octubre representado como DÍA FERIADO -->
            <div class="day-cell">
                <span>12</span>
                <div class="event-tag badge-feriado" data-id="101" data-type="feriado">
                    <i class="fa-solid fa-umbrella-beach"></i> Feriado: Resistencia Indígena
                </div>
            </div>

            <div class="day-cell"><span>13</span></div>
            <div class="day-cell"><span>14</span></div>
            <div class="day-cell">
                <span>15</span>
                <div class="event-tag badge-charla" data-id="3">Charla: IA</div>
            </div>
            <div class="day-cell"><span>16</span></div>
            <div class="day-cell"><span>17</span></div>
            <div class="day-cell"><span>18</span></div>
            <div class="day-cell"><span>19</span></div>
            <div class="day-cell"><span>20</span></div>
            <div class="day-cell"><span>21</span></div>
            <div class="day-cell"><span>22</span></div>
            <div class="day-cell"><span>23</span></div>
            <div class="day-cell"><span>24</span></div>
            <div class="day-cell"><span>25</span></div>
            <div class="day-cell"><span>26</span></div>
            <div class="day-cell"><span>27</span></div>
            <div class="day-cell"><span>28</span></div>
            <div class="day-cell"><span>29</span></div>
            <div class="day-cell"><span>30</span></div>
            <div class="day-cell"><span>31</span></div>
        </div>
    </div>
    
</div>
@push('scripts')
    <script src="{{ asset('assets/js/coordinador/calendario.js') }}"></script>
@endpush 