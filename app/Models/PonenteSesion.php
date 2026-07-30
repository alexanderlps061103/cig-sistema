<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PonenteSesion extends Model
{
    use HasFactory;

    protected $table = 'ponente_sesion';

    protected $fillable = [
        'sesion_id',
        'docente_id',
        'persona_id',
        'rol',
    ];

    // Relaciones
    public function sesion()
    {
        return $this->belongsTo(Sesion::class, 'sesion_id');
    }

    // si el ponente es un docente registrado en el sistema
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    // si el ponente es un invitado que solo tiene Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    // Helper que devuelve la entidad ponente preferente (Docente si existe, sino Persona)
    public function getPonenteAttribute()
    {
        if ($this->docente) {
            return $this->docente;
        }
        return $this->persona;
    }

    // Indica si el ponente es un docente del sistema
    public function esDocente(): bool
    {
        return ! is_null($this->docente_id);
    }
}
