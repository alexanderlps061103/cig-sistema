<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'revisado_por');
    }
}
