<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feriado extends Model
{
    use HasFactory;

    protected $table = 'feriados';

    protected $fillable = [
        'fecha',
        'descripcion',
        'recurrente',
        'creado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'recurrente' => 'boolean',
    ];

    public function creador()
    {
        return $this->belongsTo(Persona::class, 'creado_por');
    }
}
