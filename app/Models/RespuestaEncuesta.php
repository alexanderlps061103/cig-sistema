<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaEncuesta extends Model
{
    protected $table = 'respuestas_encuesta';

    protected $fillable = [
        'pregunta_id',
        'persona_id',
        'valor'
    ];

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(PreguntaEncuesta::class, 'pregunta_id');
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
