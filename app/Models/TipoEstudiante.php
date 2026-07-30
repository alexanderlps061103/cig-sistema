<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEstudiante extends Model
{
    // Definimos explícitamente el nombre de la tabla
    protected $table = 'tipo_estudiantes';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'nombre',
        'estado',
    ];

    // Casteamos el campo estado como booleano
    protected $casts = [
        'estado' => 'boolean',
    ];
}