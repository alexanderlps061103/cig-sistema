<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApproval extends Model
{
    protected $table = 'user_approvals';

    protected $fillable = [
        'persona_id',
        'tipo',
        'estado',
        'solicitado_por',
        'aprobado_por',
        'motivo',
        'fecha_solicitud',
        'fecha_respuesta'
    ];

     protected $casts = ['fecha_solicitud' => 'datetime','fecha_respuesta' => 'datetime'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
