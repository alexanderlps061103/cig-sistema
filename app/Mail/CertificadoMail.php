<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Certificado;
use Illuminate\Support\Facades\Storage;

class CertificadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Certificado $cert;

    public function __construct(Certificado $cert)
    {
        $this->cert = $cert;
    }

    public function build()
    {
        $mail = $this->subject("Tu certificado: {$this->cert->codigo_verificacion}")
            ->view('emails.certificado')
            ->with(['cert' => $this->cert]);

        if ($this->cert->archivo) {
            $path = str_replace('storage/','',$this->cert->archivo);
            if (Storage::disk('public')->exists($path)) {
                $mail->attach(storage_path("app/public/{$path}"), [
                    'as' => basename($path),
                    'mime' => 'application/pdf',
                ]);
            }
        }

        return $mail;
    }
}
