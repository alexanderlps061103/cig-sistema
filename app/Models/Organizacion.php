<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    // Nombre de la tabla en plural como definimos en la migración
    protected $table = 'organizaciones';

    protected $fillable = [
        'rif',
        'nombre_razon_social',
        'direccion',
        'telefono_contacto',
        'email_contacto',
        'persona_contacto',
        'estado'
    ];

    /**
     * Una organización puede tener muchos estudiantes haciendo pasantías.
     */
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'organizacion_id');
    }
}
