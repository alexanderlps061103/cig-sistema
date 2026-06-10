<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartaPasantia extends Model
{
    use HasFactory;

    protected $table = 'cartas_pasantia';

    protected $fillable = [
        'estudiante_id',
        'tipo',
        'institucion_destino',
        'fecha_emision',
        'archivo',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'estudiante_id');
    }
}
