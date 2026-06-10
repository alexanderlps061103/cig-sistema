<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaEncuesta extends Model
{
    use HasFactory;

    protected $table = 'respuestas_encuesta';

    protected $fillable = [
        'pregunta_id',
        'persona_id',
        'valor',
    ];

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(PreguntaEncuesta::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
