<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function expedientes()
    {
        return $this->hasMany(ExpedienteEstudiante::class, 'carrera_id');
    }

    /**
     * Estudiantes asociados indirectamente (a través de expedientes)
     */
    public function estudiantes()
    {
        return $this->hasManyThrough(
            Estudiante::class,
            ExpedienteEstudiante::class,
            'carrera_id',     
            'id',                          
            'estudiante_id'   
        );
    }
}