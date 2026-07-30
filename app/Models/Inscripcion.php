<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';
    protected $primaryKey = 'id_inscripcion';
    protected $fillable = [
        'fecha_registro', 
        'id_estudiante', 
        'id_publico_general', 
        'id_asistencia', 
        'id_actividad', 
        'id_documento', 
        'estado'
    ];

    // Relación con el estudiante (si aplica)
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }

    // Relación con la persona física (si es público general)
    public function publicoGeneral()
    {
        return $this->belongsTo(Persona::class, 'id_publico_general', 'id');
    }

    // Relación con la actividad
    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'id_actividad', 'id_actividad');
    }
}