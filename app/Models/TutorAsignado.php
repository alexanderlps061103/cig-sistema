<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorAsignado extends Model
{
    use HasFactory;

    protected $table = 'tutores_asignados';

    protected $fillable = [
        'induccion_id',
        'docente_id',
        'estudiante_id',
        'fecha_asignacion',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'activo' => 'boolean',
    ];

    public function induccion()
    {
        return $this->belongsTo(Induccion::class, 'induccion_id');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
