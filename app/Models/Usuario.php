<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    protected $fillable = [
        'persona_id',
        'email',
        'password',
        'verificado',
        'activo',
        'aprobado_por',
        'aprobado_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'verificado' => 'boolean',
        'activo' => 'boolean',
        'password' => 'hashed', // Hashing automático nativo de Laravel (Bcrypt/Argon2)
    ];

    /**
     * Relación con la Persona base del usuario.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Accesor para obtener el nombre completo.
     */
    public function getNombreCompletoAttribute(): ?string
    {
        return $this->persona ? $this->persona->nombre_completo ?? $this->persona->nombres . ' ' . $this->persona->apellidos : null;
    }

    // =========================================================================
    // RELACIONES PARA DASHBOARDS (DOCENTE Y ESTUDIANTE)
    // =========================================================================

    /**
     * Relación con el perfil de Docente asociado (vía persona_id).
     */
    public function docente(): HasOne
    {
        return $this->hasOne(Docente::class, 'persona_id', 'persona_id');
    }

    /**
     * Relación con el perfil de Estudiante asociado (vía persona_id).
     */
    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class, 'persona_id', 'persona_id');
    }

    /**
     * Obtiene las sesiones asignadas a este usuario en su rol de docente.
     */
    public function sesionesComoPonente()
    {
        $docente = $this->docente;

        if (!$docente) {
            // Retorna una consulta vacía segura en caso de que el usuario no sea docente
            return $this->belongsToMany(Sesion::class, 'ponente_sesion', 'docente_id', 'sesion_id')
                        ->whereRaw('1 = 0');
        }

        return $docente->sesionesComoPonente();
    }

    /**
     * Relación con las Inscripciones de actividades (vía persona_id).
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'persona_id', 'persona_id');
    }

    /**
     * Relación con los Certificados obtenidos por actividades o sesiones (vía persona_id).
     */
    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'persona_id', 'persona_id');
    }

     public function tutoriasAsignadas()
    {
        $docente = $this->docente;

        if (!$docente) {
            // Retorna una consulta vacía en caso de que el usuario no sea docente
            return $this->hasMany(TutorAsignado::class, 'docente_id')
                        ->whereRaw('1 = 0');
        }

        return $docente->tutoriasAsignadas();
    }
}
