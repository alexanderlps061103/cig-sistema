<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Induccion extends Model
{
    use HasFactory;

    protected $table = 'inducciones';

    protected $fillable = [
        'solicitud_id',
        'actividad_id',
        'aprobada',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'aprobada' => 'boolean',
        'fecha_aprobacion' => 'datetime',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudInduccion::class);
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }
}
