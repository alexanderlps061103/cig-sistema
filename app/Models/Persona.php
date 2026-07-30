<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Persona extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'personas';

    protected $fillable = [
        'nombres','apellidos','cedula','telefono','sexo','foto','cedula_imagen','verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // ==========================================
    // RELACIONES DE NEGOCIO
    // ==========================================

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'persona_id');
    }

    public function curriculum()
    {
        return $this->hasOne(Curriculum::class, 'persona_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'persona_rol', 'persona_id', 'rol_id')
            ->withPivot(['asignado_por','activo','asignado_en'])
            ->withTimestamps();
    }

    public function personaRols()
    {
        return $this->hasMany(PersonaRol::class, 'persona_id');
    }

    public function documentosExpediente()
    {
        return $this->hasMany(DocumentoExpediente::class, 'subido_por');
    }

    public function docentes()
    {
        return $this->hasOne(Docente::class, 'persona_id');
    }

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'persona_id');
    }

    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'persona_id');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'persona_id');
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'persona_id');
    }
}