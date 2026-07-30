<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trimestre extends Model {
    protected $table = 'trimestres';
    protected $primaryKey = 'id_trimestre';
    
    protected $fillable = [
        'nombre', 
        'fecha_inicio', 
        'fecha_fin', 
        'id_planificacion'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function planificacion(): BelongsTo {
        // Se definen explícitamente (ModeloRelacionado, Foreign Key, Owner Key)
        return $this->belongsTo(Planificacion::class, 'id_planificacion', 'id_planificacion');
    }

    public function actividades(): HasMany {
        // Se definen explícitamente (ModeloRelacionado, Foreign Key, Local Key)
        return $this->hasMany(Actividad::class, 'id_trimestre', 'id_trimestre');
    }
}