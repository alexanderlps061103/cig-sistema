<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sesion extends Model
{
    use HasFactory;

    protected $table = 'sesiones';

    protected $fillable = [
        'actividad_id',
        'numero_sesion',
        'tema',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'lugar',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function ponentes(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'ponente_sesion')
                    ->using(PonenteSesion::class)
                    ->withPivot('rol');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }
}
