<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'trimestre_id',
        'tipo_actividad_id',
        'nombre',
        'descripcion',
        'fecha_inicio_inscripcion',
        'fecha_fin_inscripcion',
        'fecha_actividad',
        'duracion_total_minutos',
        'cupos',
        'espacio_id',
        'estado',
        'qr_asistencia',
        'creado_por',
    ];

    protected $casts = [
        'fecha_inicio_inscripcion' => 'datetime',
        'fecha_fin_inscripcion' => 'datetime',
        'fecha_actividad' => 'date',
        'cupos' => 'integer',
        'duracion_total_minutos' => 'integer',
    ];

    public function trimestre(): BelongsTo
    {
        return $this->belongsTo(Trimestre::class);
    }

    public function tipoActividad(): BelongsTo
    {
        return $this->belongsTo(TipoActividad::class);
    }

    public function espacio(): BelongsTo
    {
        return $this->belongsTo(Espacio::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'creado_por');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    public function encuestas(): HasMany
    {
        return $this->hasMany(Encuesta::class);
    }
}
