<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulacionPonente extends Model
{
    protected $table = 'postulaciones_ponentes';

    protected $fillable = [
        'persona_id',
        'area_especialidad',
        'resumen_propuesta',
        'archivo_curriculum',
        'estado',
        'evaluado_por',
        'observaciones_coordinador'
    ];

    /**
     * La postulación pertenece a una persona (sus datos básicos).
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * La postulación es evaluada por el Coordinador General (otra Persona).
     */
    public function evaluador()
    {
        return $this->belongsTo(Persona::class, 'evaluado_por');
    }
}
