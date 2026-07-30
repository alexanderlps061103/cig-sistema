<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Actividad;
use App\Models\Asistencia;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Support\Str;

class CertificadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['misCertificados','descargar']);
    }

    // Generar certificados para una actividad (coordinador)
    public function generarParaActividad(Actividad $actividad)
    {
        // puedes delegar a Job GenerateCertificatesForActivity
        // aquí un flujo simple (ver reglas arriba)
        $asistencias = Asistencia::whereIn('sesion_id', $actividad->sesiones->pluck('id'))->get();
        $personas = $asistencias->groupBy('persona_id');

        foreach ($personas as $personaId => $list) {
            $persona = Persona::find($personaId);
            $cert = Certificado::create([
                'persona_id' => $persona->id,
                'tipo' => 'participante',
                'actividad_id' => $actividad->id,
                'codigo_verificacion' => Str::upper(Str::random(10)),
                'fecha_emision' => now(),
            ]);

            // generar pdf
            $pdf = PDF::loadView('certificados.plantilla', compact('cert','actividad','persona'))->setPaper('a4','landscape');
            $path = "certificados/{$actividad->id}/cert_{$cert->id}.pdf";
            Storage::disk('public')->put($path, $pdf->output());
            $cert->archivo = "storage/{$path}";
            $cert->save();

            // enqueue Mail send job (opcional)
            // dispatch(new \App\Jobs\SendCertificadoJob($cert));
        }

        return back()->with('success','Certificados generados.');
    }

    public function descargar(Certificado $cert)
    {
        $path = str_replace('storage/','',$cert->archivo);
        if (! $path || ! Storage::disk('public')->exists($path)) abort(404);
        return response()->download(storage_path("app/public/{$path}"));
    }

    // Mis certificados para usuario autenticado (estudiante/ponente)
    public function misCertificados()
    {
        $persona = auth()->user()->persona;
        $certificados = Certificado::where('persona_id',$persona->id)->latest()->paginate(20);
        return view('certificados.mis', compact('certificados'));
    }
}
