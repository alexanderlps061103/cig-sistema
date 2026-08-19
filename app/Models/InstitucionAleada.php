<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitucionAliada extends Model
{
    protected $table = 'instituciones_aliadas';
    protected $fillable = ['nombre', 'tipo', 'direccion', 'persona_contacto_fijo'];

    public function solicitudesSede() { return $this->hasMany(SolicitudUsoSede::class, 'institucion_id'); }
}
