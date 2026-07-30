<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitaCentro extends Model
{
    protected $table = 'visitas_centro';

    protected $fillable = [
        'nombre_completo',
        'cedula',
        'motivo_visita',
        'fecha_visita'
    ];

     protected $casts = ['fecha_visita' => 'date'];

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
