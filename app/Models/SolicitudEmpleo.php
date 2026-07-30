<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SolicitudEmpleo extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_empleo';

    protected $fillable = [
        'persona_id',
        'mensaje',
        'estado',
        'revisado_por',
        'fecha_revision',
    ];

    protected $casts = [
        'fecha_revision' => 'datetime',
    ];

    // Relaciones
    // postulante
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    // quien revisó la solicitud (puede ser persona/usuario/coordinador)
    public function revisadoPor()
    {
        return $this->belongsTo(Persona::class, 'revisado_por');
    }

    // Helpers
    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isAprobada(): bool
    {
        return $this->estado === 'aprobada';
    }

    public function isRechazada(): bool
    {
        return $this->estado === 'rechazada';
    }

    // Scope para filtrar por estado (tipado)
    /**
     * Filtra solicitudes por estado.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $estado
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }
}
