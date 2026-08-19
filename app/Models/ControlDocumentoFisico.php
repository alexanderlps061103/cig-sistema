<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlDocumentoFisico extends Model
{
    protected $table = 'control_documentos_fisicos';
    protected $fillable = [
        'estudiante_id',
        'carta_aceptacion_empresa',
        'evaluacion_tutor_empresarial',
        'evaluacion_tutor_institucional',
        'carta_culminacion_empresa',
        'verificado_por'
    ];

    public function estudiante()
     { return $this->belongsTo(Estudiante::class, 'estudiante_id'); }
     
    public function verificador()
    { return $this->belongsTo(Persona::class, 'verificado_por'); }
}
