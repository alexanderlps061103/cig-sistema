<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Certificado;
use App\Mail\CertificadoMail;
use Illuminate\Support\Facades\Mail;

class SendCertificadoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $certificadoId;

    public function __construct(int $certificadoId)
    {
        $this->certificadoId = $certificadoId;
        $this->queue = 'emails';
    }

    public function handle()
    {
        $cert = Certificado::with('persona.usuario','actividad')->find($this->certificadoId);
        if (! $cert) return;

        $email = optional($cert->persona->usuario)->email ?? null;
        if (! $email) return;

        Mail::to($email)->queue(new CertificadoMail($cert));
    }
}
