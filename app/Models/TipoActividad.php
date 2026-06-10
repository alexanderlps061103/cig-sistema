<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoActividad extends Model
{
    use HasFactory;

    protected $table = 'tipos_actividad';

    protected $fillable = ['nombre', 'descripcion'];

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class);
    }
}
