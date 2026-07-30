<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Sesion;
use App\Models\PonenteSesion;
use App\Models\Asistencia;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; // require maatwebsite/excel
use App\Exports\AsistenciasExport; // crea este Export
use Carbon\Carbon;

class SesionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:coordinador'])->except(['scanQr','studentScanPage']);
    }

    public function index(Actividad $actividad)
    {
        $sesiones = $actividad->sesiones()->with('ponentes.docente.persona')->orderBy('start_at')->get();
        return view('coordinador.sesiones.index', compact('actividad','sesiones'));
    }

    public function create(Actividad $actividad)
    {
        $docentes = \App\Models\Docente::with('persona')->get();
        return view('coordinador.sesiones.create', compact('actividad','docentes'));
    }

    public function store(Request $request, Actividad $actividad)
    {
        $data = $request->validate([
            'numero_sesion' => 'required|integer',
            'tema' => 'nullable|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'lugar' => 'nullable|string|max:255',
            'duracion_minutos' => 'nullable|integer',
            'ponentes' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $data['qr_token'] = Str::uuid();
            $data['qr_expires_at'] = Carbon::parse($data['start_at'])->addHours(6);
            $sesion = $actividad->sesiones()->create($data);

            if (!empty($data['ponentes'])) {
                foreach ($data['ponentes'] as $docenteId) {
                    PonenteSesion::create([
                        'sesion_id' => $sesion->id,
                        'docente_id' => $docenteId,
                        'rol' => 'ponente'
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('coordinador.sesiones.index', $actividad)->with('success','Sesión creada.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Actividad $actividad, Sesion $sesion)
    {
        $docentes = \App\Models\Docente::with('persona')->get();
        return view('coordinador.sesiones.edit', compact('actividad','sesion','docentes'));
    }

    public function update(Request $request, Actividad $actividad, Sesion $sesion)
    {
        $data = $request->validate([
            'numero_sesion' => 'required|integer',
            'tema' => 'nullable|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'lugar' => 'nullable|string|max:255',
            'duracion_minutos' => 'nullable|integer',
            'ponentes' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $sesion->update($data);

            if (isset($data['ponentes'])) {
                // sincronización simple: eliminar y volver a crear
                $sesion->ponentes()->delete();
                foreach ($data['ponentes'] as $docenteId) {
                    PonenteSesion::create(['sesion_id'=>$sesion->id,'docente_id'=>$docenteId,'rol'=>'ponente']);
                }
            }
            DB::commit();
            return redirect()->route('coordinador.sesiones.index', $actividad)->with('success','Sesión actualizada.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error'=>$e->getMessage()]);
        }
    }

    public function destroy(Actividad $actividad, Sesion $sesion)
    {
        $sesion->delete();
        return back()->with('success','Sesión eliminada.');
    }

    // Endpoint que el estudiante usa para ver la página de escanear QR
    public function studentScanPage(Sesion $sesion)
    {
        // vista para estudiante con QR scanner JS
        return view('estudiante.scan', compact('sesion'));
    }

    // POST: escaneo por parte del estudiante o app móvil
    public function scanQr(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $qr = $request->input('qr_token');
        $sesion = Sesion::where('qr_token',$qr)->first();

        if (! $sesion) {
            return response()->json(['ok'=>false,'message'=>'QR inválido.'], 404);
        }

        // comprobar expiración
        if ($sesion->qr_expires_at && now()->gt($sesion->qr_expires_at)) {
            return response()->json(['ok'=>false,'message'=>'QR expirado.'], 400);
        }

        $persona = auth()->user()->persona ?? null;
        if (! $persona) {
            return response()->json(['ok'=>false,'message'=>'Debes iniciar sesión para registrar tu asistencia.'], 401);
        }

        // si ya existe asistencia para esta persona y sesion, devolver ok
        $existe = Asistencia::where('sesion_id',$sesion->id)->where('persona_id',$persona->id)->first();
        if ($existe) {
            return response()->json(['ok'=>true,'message'=>'Asistencia ya registrada.']);
        }

        $as = Asistencia::create([
            'sesion_id' => $sesion->id,
            'persona_id' => $persona->id,
            'fecha_hora' => now(),
            'metodo' => 'qr',
            'registrado_por' => $persona->id,
            'minutes_attended' => $request->input('minutes_attended', null)
        ]);

        // opcional: broadcast event para actualizar listado en tiempo real
        // event(new \App\Events\AsistenciaRegistrada($as));

        return response()->json(['ok'=>true,'message'=>'Asistencia registrada.']);
    }

    // Exportar asistencias a Excel (docente o coordinador)
    public function exportAsistencias(Sesion $sesion)
    {
        // requires maatwebsite/excel and an export class App\Exports\AsistenciasExport
        return Excel::download(new AsistenciasExport($sesion->id), "asistencias_sesion_{$sesion->id}.xlsx");
    }

    // Regenerar QR
    public function regenerarQr(Sesion $sesion)
    {
        $sesion->update(['qr_token' => Str::uuid(), 'qr_expires_at' => now()->addHours(6)]);
        return back()->with('success','QR regenerado.');
    }
}
