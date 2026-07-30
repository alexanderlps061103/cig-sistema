<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Induccion extends Model
{
    use HasFactory;

    protected $table = 'inducciones';

    protected $fillable = [
        'solicitud_id',
        'actividad_id',
        'estudiante_id',
        'tutor_id',
        'aprobada',
        'horas_completadas',
        'duracion_minutos',
        'fecha_aprobacion',
        'observaciones',
    ];

    protected $casts = [
        'aprobada' => 'boolean',
        'fecha_aprobacion' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudInduccion::class, 'solicitud_id');
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function tutor()
    {
        return $this->belongsTo(Docente::class, 'tutor_id');
    }

    public function tutoresAsignados()
    {
        return $this->hasMany(TutorAsignado::class, 'induccion_id');
    }
}
