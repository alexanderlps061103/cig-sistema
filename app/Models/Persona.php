<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Authenticatable
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula',
        'telefono',
        'email',
        'password',
        'sexo',
        'foto',
        'cedula_imagen',
        'verificado',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'verificado' => 'boolean',
        'activo' => 'boolean',
    ];

    // Roles de la persona
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'persona_rol');
    }

    // Si es estudiante, puede tener datos extendidos
    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class);
    }

    // Inscripciones a actividades
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    // Asistencias registradas
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    // Certificados recibidos
    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    // Sesiones como ponente (vía tabla pivote)
    public function sesionesComoPonente(): BelongsToMany
    {
        return $this->belongsToMany(Sesion::class, 'ponente_sesion')
                    ->using(PonenteSesion::class)
                    ->withPivot('rol');
    }

    // Solicitudes de inducción (si es estudiante)
    public function solicitudesInduccion(): HasMany
    {
        return $this->hasMany(SolicitudInduccion::class, 'estudiante_id');
    }

    // Tutorías donde es tutor
    public function tutoriasAsignadas(): HasMany
    {
        return $this->hasMany(TutoresAsignado::class, 'tutor_id');
    }

    // Cartas de pasantía emitidas para este estudiante
    public function cartasPasantia(): HasMany
    {
        return $this->hasMany(CartaPasantia::class, 'estudiante_id');
    }

    // Respuestas a encuestas
    public function respuestasEncuesta(): HasMany
    {
        return $this->hasMany(RespuestaEncuesta::class);
    }

    // Solicitud de empleo (para ser docente)
    public function solicitudEmpleo(): HasOne
    {
        return $this->hasOne(SolicitudEmpleo::class);
    }

    // Currículum (si es docente)
    public function curriculum(): HasOne
    {
        return $this->hasOne(Curriculum::class);
    }
}
