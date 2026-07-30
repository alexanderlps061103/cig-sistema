<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoActividad extends Model {
    protected $table = 'tipo_actividades';
    protected $primaryKey = 'id_tipo_actividad';
    
    protected $fillable = [
        'nombre', 
        'descripcion', 
        'duracion', 
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}