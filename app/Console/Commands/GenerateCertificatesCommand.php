<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Actividad;
use App\Jobs\GenerateCertificatesForActivity;
use Carbon\Carbon;

class GenerateCertificatesCommand extends Command
{
    protected $signature = 'certificados:generar {--hours=24 : Buscar sesiones finalizadas en las últimas X horas}';
    protected $description = 'Genera certificados para actividades cuyas sesiones han finalizado y no tienen certificados';

    public function handle()
    {
        $hours = (int)$this->option('hours');
        $cutoff = Carbon::now()->subHours($hours);

        // Obtener actividades con al menos una sesión que finalizó antes del cutoff
        $actividades = Actividad::whereHas('sesiones', function ($q) use ($cutoff) {
            $q->whereNotNull('end_at')->where('end_at', '<=', $cutoff);
        })->get();

        $this->info("Actividades detectadas: " . $actividades->count());

        foreach ($actividades as $actividad) {
            // Si ya existen certificados para la actividad saltar
            $exists = $actividad->certificados()->exists();
            if ($exists) {
                $this->info("Actividad {$actividad->id} ya tiene certificados, saltando.");
                continue;
            }

            GenerateCertificatesForActivity::dispatch($actividad->id);
            $this->info("Dispatch job GenerateCertificatesForActivity para actividad {$actividad->id}");
        }

        return 0;
    }
}
