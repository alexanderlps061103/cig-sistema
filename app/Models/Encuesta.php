<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encuesta extends Model
{
    protected $table = 'encuestas';

    protected $fillable = [
        'actividad_id',
        'descripcion',
        'fecha_limite'
    ];

    protected $casts = ['fecha_limite' => 'datetime'];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(PreguntaEncuesta::class, 'encuesta_id');
    }
}
