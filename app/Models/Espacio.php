<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Espacio extends Model
{
    protected $table = 'espacios';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'estado'    => 'boolean',
    ];

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'espacio_id');
    }
}
