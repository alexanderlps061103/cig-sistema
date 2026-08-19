<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $fillable = ['persona_id','carrera_id','modalidad_egreso','es_regular', 'organizacion_id'];

    protected $casts = [
        'es_regular' => 'boolean',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function expediente()
    {
        return $this->hasOne(ExpedienteEstudiante::class, 'estudiante_id');
    }

    public function organizacion() { return $this->belongsTo(Organizacion::class, 'organizacion_id'); }

    public function controlFisico() { return $this->hasOne(ControlDocumentoFisico::class, 'estudiante_id'); }

    public function acreditacion() { return $this->hasOne(AcreditacionExperiencia::class, 'estudiante_id'); }

    public function inducciones()
    {
        return $this->hasMany(Induccion::class, 'estudiante_id');
    }

    public function cartasPasantia()
    {
        return $this->hasMany(CartaPasantia::class, 'estudiante_id');
    }
}
