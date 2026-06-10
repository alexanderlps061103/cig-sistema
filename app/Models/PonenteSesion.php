<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PonenteSesion extends Pivot
{
    use HasFactory;

    protected $table = 'ponente_sesion';

    protected $fillable = [
        'sesion_id',
        'persona_id',
        'rol',
    ];
}
