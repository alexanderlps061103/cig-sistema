<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutoresAsignado extends Model
{
    use HasFactory;

    protected $table = 'tutores_asignados'; // coincide con pluralización

    protected $fillable = [
        'estudiante_id',
        'tutor_id',
        'fecha_asignacion',
        'activo',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'activo' => 'boolean',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'estudiante_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'tutor_id');
    }
}
