<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudUsoSede extends Model
{
    protected $table = 'solicitudes_uso_sede';
    protected $fillable = [
        'institucion_id',
        'solicitante_persona_id',
        'ponente_persona_id',
        'id_salon',
        'fecha_actividad',
        'poblacion_atendida',
        'detalle_colaboracion',
        'estado'
    ];

    public function institucion() { return $this->belongsTo(InstitucionAliada::class, 'institucion_id'); }
    public function solicitante() { return $this->belongsTo(Persona::class, 'solicitante_persona_id'); }
    public function ponente() { return $this->belongsTo(Persona::class, 'ponente_persona_id'); }
    public function salon() { return $this->belongsTo(Salon::class, 'id_salon', 'id_salon'); }
}
