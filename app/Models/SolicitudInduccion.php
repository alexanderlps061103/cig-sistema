<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitudInduccion extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_induccion';

    protected $fillable = [
        'estudiante_id',
        'estado',
        'observacion',
        'fecha_solicitud',
        'fecha_respuesta',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'estudiante_id');
    }

    public function inducciones(): HasMany
    {
        return $this->hasMany(Induccion::class, 'solicitud_id');
    }
}
