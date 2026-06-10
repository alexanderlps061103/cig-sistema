<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificados';

    protected $fillable = [
        'persona_id',
        'tipo',
        'actividad_id',
        'sesion_id',
        'codigo_verificacion',
        'fecha_emision',
        'archivo',
        'firma_sello',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }
}
