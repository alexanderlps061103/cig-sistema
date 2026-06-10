<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'sesion_id',
        'persona_id',
        'fecha_hora',
        'metodo',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'registrado_por');
    }
}
