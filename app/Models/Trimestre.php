<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trimestre extends Model
{
    use HasFactory;

    protected $table = 'trimestres';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'creado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'creado_por');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class);
    }
}
