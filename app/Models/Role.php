<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function personas()
    {
        return $this->belongsToMany(Persona::class, 'persona_rol', 'rol_id', 'persona_id')
            ->withPivot(['asignado_por','activo','asignado_en'])
            ->withTimestamps();
    }
}
