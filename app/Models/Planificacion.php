<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planificacion extends Model {
    protected $table = 'planificaciones';
    protected $primaryKey = 'id_planificacion';
    
    protected $fillable = [
        'titulo', 
        'anio', // Requerido aquí para permitir la asignación masiva desde el controlador
        'fecha_creacion'
    ];

    protected $casts = [
        'fecha_creacion' => 'date',
        'anio'           => 'integer',
    ];

    public function trimestres(): HasMany {
        return $this->hasMany(Trimestre::class, 'id_planificacion', 'id_planificacion');
    }
}