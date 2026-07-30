<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudInduccion extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_induccion';

    protected $fillable = [
        'estudiante_id',
        'expediente_id',
        'tipo',
        'estado',
        'observacion',
        'fecha_solicitud',
        'fecha_respuesta',
        'cartas_activas',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
        'cartas_activas' => 'integer',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function expediente()
    {
        return $this->belongsTo(ExpedienteEstudiante::class, 'expediente_id');
    }

    public function induccion()
    {
        return $this->hasOne(Induccion::class, 'solicitud_id');
    }
}
