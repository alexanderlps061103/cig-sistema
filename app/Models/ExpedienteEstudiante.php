<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedienteEstudiante extends Model
{
    use HasFactory;

    protected $table = 'expedientes_estudiante';

    protected $fillable = [
        'estudiante_id',
        'carrera_id',
        'organizacion_id',
        'ruta_carnet',
        'carnet_verificado_at',
        'ruta_notas_certificadas',
        'notas_verificadas_at',
        'tipo_solicitud',
        'estado_solicitud',
        'tutor_asignado_id',
        'observaciones',
    ];

    protected $casts = [
        'carnet_verificado_at' => 'datetime',
        'notas_verificadas_at' => 'datetime',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoExpediente::class, 'expediente_id');
    }

    public function tutorAsignado()
    {
        return $this->belongsTo(Docente::class, 'tutor_asignado_id');
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }
}
