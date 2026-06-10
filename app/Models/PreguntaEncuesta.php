<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreguntaEncuesta extends Model
{
    use HasFactory;

    protected $table = 'preguntas_encuesta';

    protected $fillable = [
        'encuesta_id',
        'texto',
        'tipo',
        'opciones',
        'orden',
    ];

    protected $casts = [
        'opciones' => 'json',
    ];

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class);
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaEncuesta::class, 'pregunta_id');
    }
}
