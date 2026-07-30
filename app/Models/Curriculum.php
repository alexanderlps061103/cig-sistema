<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Curriculum extends Model
{
    protected $table = 'curriculums';

    protected $fillable = [
        'persona_id',
        'especialidad',
        'experiencia',
        'archivo_cv',
        'notas_internas'
    ];

    protected $casts = [
        // no hay timestamps especiales por ahora
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
