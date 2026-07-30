<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonaRol extends Model
{
    use HasFactory;

    protected $table = 'persona_rol';

    protected $fillable = ['persona_id','rol_id','asignado_por','activo','asignado_en'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function asignadoPor()
    {
        return $this->belongsTo(Persona::class, 'asignado_por');
    }
}
