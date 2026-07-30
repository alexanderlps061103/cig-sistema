<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreguntaEncuesta extends Model
{
    protected $table = 'preguntas_encuesta';

    protected $fillable = [
        'encuesta_id',
        'texto',
        'tipo',
        'opciones',
        'orden'
    ];

    protected $casts = [
        'opciones' => 'array',
    ];

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class, 'encuesta_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaEncuesta::class, 'pregunta_id');
    }
}
