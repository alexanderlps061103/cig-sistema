<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model {
    protected $table = 'actividades';
    protected $primaryKey = 'id_actividad';
    
    protected $fillable = [
        'nombre', 
        'descripcion', 
        'fecha_inscripcion_inicio', 
        'fecha_inscripcion_fin',
        'fecha', 
        'hora_inicio', 
        'hora_fin', 
        'estado', 
        'id_modalidad', 
        'id_salon', 
        'id_tipo_actividad', 
        'id_tipo_documento', 
        'id_tema', 
        'id_trimestre',
        'id_organizador'
    ];

    protected $casts = [
        'fecha'                    => 'date',
        'fecha_inscripcion_inicio' => 'date',
        'fecha_inscripcion_fin'    => 'date',
    ];

    public function tipo(): BelongsTo {
        return $this->belongsTo(TipoActividad::class, 'id_tipo_actividad', 'id_tipo_actividad');
    }

    public function salon(): BelongsTo {
        return $this->belongsTo(Salon::class, 'id_salon', 'id_salon');
    }

    // NUEVO: Relación singular para cargar el tema principal en el panel del Rector
    public function tema(): BelongsTo {
        return $this->belongsTo(Tema::class, 'id_tema', 'id_tema');
    }

    // Relación principal hacia temas (HasMany)
    public function temas(): HasMany {
        return $this->hasMany(Tema::class, 'id_actividad', 'id_actividad');
    }

    // ALIAS: Para compatibilidad con el JS que busca la relación 'sesiones'
    public function sesiones(): HasMany {
        return $this->hasMany(Tema::class, 'id_actividad', 'id_actividad');
    }

    public function trimestre(): BelongsTo {
        return $this->belongsTo(Trimestre::class, 'id_trimestre', 'id_trimestre');
    }

    // Relación original
    public function modalidadRelacion(): BelongsTo {
        return $this->belongsTo(Modalidad::class, 'id_modalidad', 'id_modalidad');
    }

    // ALIAS: Para evitar que se muestre "N/A" al buscar la relación directa 'modalidad'
    public function modalidad(): BelongsTo {
        return $this->belongsTo(Modalidad::class, 'id_modalidad', 'id_modalidad');
    }

    public function tipoDocumento(): BelongsTo {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }

    public function inscripciones(): HasMany {
        return $this->hasMany(Inscripcion::class, 'id_actividad', 'id_actividad');
    }

    // Relación con el Organizador de la actividad (Persona)
    public function organizador(): BelongsTo {
        return $this->belongsTo(Persona::class, 'id_organizador', 'id');
    }

    // NUEVO ALIAS: Para el listado de actividades en los reportes
    public function creador(): BelongsTo {
        return $this->belongsTo(Persona::class, 'id_organizador', 'id');
    }
}