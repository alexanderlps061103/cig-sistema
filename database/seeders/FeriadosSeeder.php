<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Feriado;

class FeriadosSeeder extends Seeder
{
    public function run(): void
    {
        $year = 2026;

        // Usamos la función easter_date si está disponible en PHP para obtener timestamp de Pascua
        // fallback a algoritmo simple si no la tuvieras
        $easterTimestamp = function_exists('easter_date') ? easter_date($year) : strtotime($this->calculateEaster($year));
        $easter = Carbon::createFromTimestamp($easterTimestamp)->startOfDay();

        // Fechas móviles relativas a Pascua
        $carnivalMonday = $easter->copy()->subDays(48);
        $carnivalTuesday = $easter->copy()->subDays(47);
        $maundyThursday = $easter->copy()->subDays(3); // Jueves Santo
        $goodFriday = $easter->copy()->subDays(2);     // Viernes Santo

        // Feriados fijos y calculados (lista base para Venezuela — ajusta según normativa local)
        $feriados = [
            // Fijos
            ['fecha' => Carbon::create($year, 1, 1)->toDateString(), 'descripcion' => 'Año Nuevo', 'recurrente' => true],
            // Carnaval (móviles)
            ['fecha' => $carnivalMonday->toDateString(), 'descripcion' => 'Carnaval (Lunes)', 'recurrente' => false],
            ['fecha' => $carnivalTuesday->toDateString(), 'descripcion' => 'Carnaval (Martes)', 'recurrente' => false],
            // Semana Santa
            ['fecha' => $maundyThursday->toDateString(), 'descripcion' => 'Jueves Santo', 'recurrente' => false],
            ['fecha' => $goodFriday->toDateString(), 'descripcion' => 'Viernes Santo', 'recurrente' => false],
            // Declaración de Independencia (19 Abril)
            ['fecha' => Carbon::create($year, 4, 19)->toDateString(), 'descripcion' => 'Declaración de Independencia', 'recurrente' => true],
            // Día del Trabajador (1 Mayo)
            ['fecha' => Carbon::create($year, 5, 1)->toDateString(), 'descripcion' => 'Día del Trabajador', 'recurrente' => true],
            // Batalla de Carabobo (24 Junio)
            ['fecha' => Carbon::create($year, 6, 24)->toDateString(), 'descripcion' => 'Batalla de Carabobo', 'recurrente' => true],
            // Día de la Independencia (5 Julio)
            ['fecha' => Carbon::create($year, 7, 5)->toDateString(), 'descripcion' => 'Día de la Independencia', 'recurrente' => true],
            // Natalicio de Simón Bolívar (24 Julio)
            ['fecha' => Carbon::create($year, 7, 24)->toDateString(), 'descripcion' => 'Natalicio de Simón Bolívar', 'recurrente' => true],
            // Día de la Resistencia Indígena (12 Octubre)
            ['fecha' => Carbon::create($year, 10, 12)->toDateString(), 'descripcion' => 'Día de la Resistencia Indígena', 'recurrente' => true],
            // Navidad (25 Dic)
            ['fecha' => Carbon::create($year, 12, 25)->toDateString(), 'descripcion' => 'Navidad', 'recurrente' => true],
        ];

        // Inserta (evita duplicados en la misma fecha)
        foreach ($feriados as $f) {
            Feriado::updateOrCreate(
                ['fecha' => $f['fecha']],
                ['descripcion' => $f['descripcion'], 'recurrente' => (bool)$f['recurrente']]
            );
        }
    }

    // Algoritmo de respaldo para calcular Pascua si easter_date no existe
    protected function calculateEaster(int $Y): string
    {
        // Meeus/Jones/Butcher algorithm (retorna fecha yyyy-mm-dd)
        $a = $Y % 19;
        $b = intdiv($Y, 100);
        $c = $Y % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2*$e + 2*$i - $h - $k) % 7;
        $m = intdiv($a + 11*$h + 22*$l, 451);
        $month = intdiv($h + $l - 7*$m + 114, 31);
        $day = (($h + $l - 7*$m + 114) % 31) + 1;
        return sprintf('%04d-%02d-%02d', $Y, $month, $day);
    }
}
