<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Actividad;
use App\Models\Asistencia;
use App\Models\Certificado;
use App\Models\Persona;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;
use Exception;

use App\Jobs\SendCertificadoJob;

class GenerateCertificatesForActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $actividadId;

    public function __construct(int $actividadId)
    {
        $this->actividadId = $actividadId;
        $this->queue = 'default';
    }

    public function handle()
    {
        $actividad = Actividad::with('sesiones.ponentes')->find($this->actividadId);
        if (! $actividad) return;

        // obtener asistentes por Asistencia en cualquier sesión de la actividad
        $sesionIds = $actividad->sesiones->pluck('id')->toArray();
        $asistencias = Asistencia::whereIn('sesion_id', $sesionIds)
            ->with('persona')
            ->get()
            ->groupBy('persona_id');

        foreach ($asistencias as $personaId => $lista) {
            try {
                $persona = Persona::find($personaId);
                if (! $persona) continue;

                // regla mínima: al menos 1 asistencia -> emitir (ajusta a tu criterio)
                $cert = Certificado::create([
                    'persona_id' => $persona->id,
                    'tipo' => 'participante',
                    'actividad_id' => $actividad->id,
                    'sesion_id' => null,
                    'codigo_verificacion' => Str::upper(Str::random(10)),
                    'fecha_emision' => now(),
                    'aprobado_por' => null,
                ]);

                $pdf = PDF::loadView('certificados.plantilla', [
                    'persona' => $persona,
                    'actividad' => $actividad,
                    'certificado' => $cert,
                ])->setPaper('a4','landscape');

                $filename = "certificados/{$actividad->id}/certificado_{$cert->id}.pdf";
                Storage::disk('public')->put($filename, $pdf->output());
                $cert->archivo = "storage/{$filename}";
                $cert->save();

                // Encolar envío de correo
                SendCertificadoJob::dispatch($cert->id);

            } catch (Exception $e) {
                // logs si quieres
                \Log::error("Error generando certificado para actividad {$this->actividadId} persona {$personaId}: ".$e->getMessage());
            }
        }

        // Ponentes: un certificado por sesión/ponente
        foreach ($actividad->sesiones as $sesion) {
            foreach ($sesion->ponentes as $ponente) {
                try {
                    $persona = $ponente->persona ?? ($ponente->docente->persona ?? null);
                    if (! $persona) continue;

                    $cert = Certificado::create([
                        'persona_id' => $persona->id,
                        'tipo' => 'ponente',
                        'actividad_id' => $actividad->id,
                        'sesion_id' => $sesion->id,
                        'codigo_verificacion' => Str::upper(Str::random(10)),
                        'fecha_emision' => now(),
                        'aprobado_por' => null,
                    ]);

                    $pdf = PDF::loadView('certificados.plantilla', [
                        'persona' => $persona,
                        'actividad' => $actividad,
                        'sesion' => $sesion,
                        'certificado' => $cert,
                    ])->setPaper('a4','landscape');

                    $filename = "certificados/{$actividad->id}/certificado_{$cert->id}.pdf";
                    Storage::disk('public')->put($filename, $pdf->output());
                    $cert->archivo = "storage/{$filename}";
                    $cert->save();

                    SendCertificadoJob::dispatch($cert->id);
                } catch (Exception $e) {
                    \Log::error("Error generando certificado ponente: ".$e->getMessage());
                }
            }
        }
    }
}
