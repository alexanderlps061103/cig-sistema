<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    protected $table = 'organizaciones';
    protected $fillable = ['rif', 'nombre_razon_social', 'direccion', 'telefono_contacto', 'persona_contacto'];

    // Relación con los procesos de acreditación
    public function acreditaciones() {
        return $this->hasMany(AcreditacionExperiencia::class, 'organizacion_id');
    }

    // Relación con los expedientes de pasantía regular
    public function expedientes() {
        return $this->hasMany(ExpedienteEstudiante::class, 'organizacion_id');
    }
}
