<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Espacio extends Model
{
    use HasFactory;

    protected $table = 'espacios';

    protected $fillable = ['nombre', 'ubicacion', 'capacidad', 'descripcion', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class);
    }
}
