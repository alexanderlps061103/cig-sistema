<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcreditacionExperiencia extends Model
{
    protected $table = 'acreditaciones_experiencia';

    protected $fillable = [
        'estudiante_id',
        'motivos_solicitud',
        'archivo_formato_1',
        'archivo_evidencias_digitales',
        'estado_proceso'
    ];

    /**
     * La acreditación pertenece a un estudiante específico.
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
